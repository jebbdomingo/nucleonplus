<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerDragonpay extends ComKoowaControllerModel
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.verifyonlinepayment', '_validateVerify');
        $this->addCommandCallback('before.showstatus', '_validateReturnUrl');

        // Sales Receipt Service
        $identifier = $this->getIdentifier($config->salesreceipt_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceSalesreceiptInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceSalesreceiptInterface"
            );
        }
        else $this->_salesreceipt_service = $service;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'salesreceipt_service' => 'com://admin/nucleonplus.accounting.service.salesreceipt'
        ));

        parent::_initialize($config);
    }

    /**
     * Validate payment
     *
     * @param KControllerContextInterface $context
     *
     * @throws KControllerExceptionRequestInvalid
     * 
     * @return void
     */
    protected function _validateVerify(KControllerContextInterface $context)
    {
        $data       = $context->request->data;
        $orders     = $this->getObject('com://admin/nucleonplus.model.orders')->id($data->txnid)->fetch();
        $translator = $this->getObject('translator');

        foreach ($orders as $order)
        {
            // Check order status if it can be verified
            if ($order->order_status <> 'awaiting_verification') {
                throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Awaiting Verification" status can be verified.' . ' Order #' . $order->id));
            }

            $inventory = $this->getObject('com://admin/nucleonplus.accounting.service.inventory');
            foreach ($order->getOrderItems() as $orderItem)
            {
                $item  = $this->getObject('com://admin/qbsync.model.items')->ItemRef($orderItem->ItemRef)->fetch();

                if (count($item) === 0) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Item'));
                }

                // Check inventory for available stock
                if (!$inventory->hasAvailableStock($item->ItemRef, $item->quantity)) {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Insufficient stock of {$item->item_name}"));
                }
            }

            // Validate digest from dragonpay
            $config     = $this->getObject('com://admin/nucleonplus.model.configs')->item('dragonpay')->fetch();
            $dragonpay  = $config->getJsonValue();
            $parameters = array(
                'txnid'    => $data->txnid,
                'refno'    => $data->refno,
                'status'   => $data->status,
                'message'  => $data->message,
                'password' => $dragonpay->password
            );
            $digestStr = implode(':', $parameters);
            $digest    = sha1($digestStr);

            if (getenv('APP_ENV') != 'production') {
                var_dump($digest);
            }

            if ($data->digest !== $digest) {
                die('result=FAIL_DIGEST_MISMATCH');
            }
        }
    }

    protected function _validateReturnUrl(KControllerContextInterface $context)
    {
        // Validate digest from dragonpay
        $data       = $context->request->data;
        $config     = $this->getObject('com://admin/nucleonplus.model.configs')->item('dragonpay')->fetch();
        $dragonpay  = $config->getJsonValue();
        $parameters = array(
            'txnid'    => $data->txnid,
            'refno'    => $data->refno,
            'status'   => $data->status,
            'message'  => $data->message,
            'password' => $dragonpay->password
        );
        $digestStr = implode(':', $parameters);
        $digest    = sha1($digestStr);

        if ($data->digest !== $digest) {
            throw new KControllerExceptionRequestInvalid('FAIL_DIGEST_MISMATCH');
        }
    }

    protected function _actionVerifyonlinepayment(KControllerContextInterface $context)
    {
        $data     = $context->request->data;
        $data->id = $data->txnid;

        if ($data->status == 'P')
        {
            $data->payment_status = $data->status;
            $order = parent::_actionEdit($context);
        }
        elseif ($data->status == 'S')
        {
            // Mark as Paid
            $data->invoice_status = 'paid';
            $data->order_status   = 'processing';
            $data->payment_status = $data->status;

            // Fetch after edit to get the joined columns
            $order = parent::_actionEdit($context);
            $order = $this->getObject('com://admin/nucleonplus.model.orders')->id($order->id)->fetch();

            // Record dragonpay payment transaction
            $this->_recordPaymentStatus($data);

            // Record transaction to accounting books
            $this->_salesreceipt_service->recordSale($order);

            // Automatically activate reward
            $this->_activateReward($order);
            
            $this->_logout();
        }
        else throw new Exception('FAIL_INVALID_STATUS');

        return $order;
    }

    protected function _recordPaymentStatus($data)
    {
        $controller       = $this->getObject('com:dragonpay.controller.payment');
        $dragonpayPayment = $this->getObject('com:dragonpay.model.payments')->tnxid($data->txnid)->fetch();

        if ($dragonpayPayment->isNew())
        {
            $data->id = $data->txnid;
            $controller->add($data->toArray());
        }
        else
        {
            $controller
                ->id($data->txnid)
                ->edit($data->toArray())
            ;
        }
    }

    protected function _actionShowstatus(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $orders = $this->getModel()->fetch();
        } else {
            $orders = $context->result;
        }

        return $orders;
    }

    /**
     * Activates the reward
     *
     * @param   KModelEntityInterface $order
     * 
     * @throws  KControllerExceptionRequestInvalid
     * @throws  KControllerExceptionResourceNotFound
     * 
     * @return  boolean
     */
    protected function _activateReward(KModelEntityInterface $order)
    {
        $translator = $this->getObject('translator');

        // Check order status if its reward can be activated
        if (!in_array($order->order_status, array('processing', 'completed'))) {
            throw new KControllerExceptionRequestInvalid($translator->translate("Unable to activate corresponding reward: Order #{$order->id} should be in \"Processing\" status"));
        }

        // Try to activate reward
        $rewards = $order->getRewards();
        foreach ($rewards as $reward) {
            $this->getObject('com://admin/nucleonplus.controller.reward')->id($reward->id)->activate();
        }
    }
}
