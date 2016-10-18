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
        // $this->addCommandCallback('before.showstatus', '_validateReturnUrl');

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

        // Reward service
        $this->_reward = $config->reward;
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
            'salesreceipt_service' => 'com://admin/nucleonplus.accounting.service.salesreceipt',
            'reward'               => 'com://admin/nucleonplus.mlm.packagereward'
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
        if (!$context->result instanceof KModelEntityInterface) {
            $orders = $this->getModel()->fetch();
        } else {
            $orders = $context->result;
        }

        $translator = $this->getObject('translator');

        foreach ($orders as $order)
        {
            // Check order status if it can be verified
            if ($order->order_status <> 'awaiting_verification') {
                throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Awaiting Verification" status can be verified'));
            }

            // Check inventory for available stock
            foreach ($order->getOrderItems() as $item)
            {
                $package  = $this->getObject('com:nucleonplus.model.packages')->id($item->package_id)->fetch();

                if (count($package) === 0) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Product Pack'));
                }

                // Check inventory for available stock
                foreach ($package->getItems() as $item)
                {
                    if (!$item->hasAvailableStock()) {
                        throw new KControllerExceptionRequestInvalid($translator->translate("Insufficient stock of {$item->_item_name}"));
                    }
                }
            }

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
        $data = $context->request->data;
        
        // Mark as Paid
        $data->invoice_status = 'paid';
        $data->order_status   = 'processing';

        $order = parent::_actionEdit($context);

        try
        {
            $this->_salesreceipt_service->recordSale($order);

            // Automatically activate reward
            $this->_activateReward($order);
        }
        catch (Exception $e)
        {
            die('result=FAIL_INTERNAL_ERROR');
        }

        die('result=OK');
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
     * @return  void
     */
    protected function _activateReward(KModelEntityInterface $order)
    {
        $translator = $this->getObject('translator');

        // Check order status if its reward can be activated
        if (!in_array($order->order_status, array('processing', 'completed'))) {
            throw new KControllerExceptionRequestInvalid($translator->translate("Unable to activate corresponding reward: Order #{$order->id} should be in \"Processing\" status"));
        }

        // Try to activate reward
        $rewards = $this->getObject('com://admin/nucleonplus.model.rewards')->product_id($order->id)->fetch();

        foreach ($rewards as $reward)
        {
            $this->getObject('com:nucleonplus.controller.reward')->id($reward->id)->activate();
            $this->getResponse()->addMessage("Reward #{$reward->id} has been activated");
        }
    }
}