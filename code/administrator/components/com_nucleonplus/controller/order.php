<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */


/**
 * Order Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComNucleonplusControllerOrder extends ComKoowaControllerModel
{
    /**
     * Sales Receipt Service
     *
     * @var ComNucleonplusAccountingServiceSalesreceiptInterface
     */
    protected $_salesreceipt_service;

    /**
     * Reward controller identifier
     *
     * @var string
     */
    protected $_reward;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.add', '_validate');
        $this->addCommandCallback('before.verifypayment', '_validateVerify');
        $this->addCommandCallback('before.markdelivered', '_validateDelivered');
        $this->addCommandCallback('before.markcompleted', '_validateCompleted');
        $this->addCommandCallback('before.void', '_validateVoid');

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
            'salesreceipt_service' => 'com:nucleonplus.accounting.service.salesreceipt',
            'inventory_service'    => 'com:nucleonplus.accounting.service.inventory',
            'reward'               => 'com:nucleonplus.rebate.packagereward',
        ));

        parent::_initialize($config);
    }

    /**
     * Validate add
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validate(KControllerContextInterface $context)
    {
        if(!$context->result instanceof KModelEntityInterface) {
            $entity = $this->getModel()->create($context->request->data->toArray());
        } else {
            $entity = $context->result;
        }

        $result = true;

        try
        {
            $translator = $this->getObject('translator');
            $account_id = (int) trim($entity->account_id);
            $package_id = (int) trim($entity->package_id);
            $package    = $this->getObject('com:nucleonplus.model.packages')->id($package_id)->fetch();
            
            // Validate account
            $account = $this->getObject('com:nucleonplus.model.accounts')->id($account_id)->fetch();
            if (count($account) === 0)
            {
                throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Account'));
                $result = false;
            }

            // Validate package id and if the member has current order
            if (empty(trim($entity->package_id)))
            {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please select a Product Pack'));
                $result = false;
            }
            elseif ($this->getModel('com:nucleonplus.model.orders')->hasCurrentOrder($account_id))
            {
                throw new KControllerExceptionRequestInvalid($translator->translate('Only one product package can be purchased per account per day'));
                $result = false;
            }
            elseif (count($package) === 0)
            {
                throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Product Pack'));
                $result = false;
            }

            // Check inventory
            foreach ($package->getItems() as $item)
            {
                if (!$item->hasAvailableStock())
                {
                    throw new KControllerExceptionActionFailed($translator->translate("Insufficient stock of {$item->_item_name}"));
                    $result = false;
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        if ($entity->form_type == 'pos')
        {
            $order_status    = 'completed';
            $invoice_status  = 'paid';
            $payment_method  = 'cash';
            $shipping_method = 'na';
        }
        else
        {
            $order_status    = 'awaiting_payment';
            $invoice_status  = 'sent';
            $payment_method  = 'deposit';
            $shipping_method = 'xend';
        }

        $data = new KObjectConfig([
            'account_id'         => $account->id,
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->price,
            'order_status'       => $order_status,
            'invoice_status'     => $invoice_status,
            'payment_method'     => $payment_method,
            'shipping_method'    => $shipping_method,
            'form_type'          => $entity->form_type,
            'tracking_reference' => $entity->tracking_reference,
            'payment_reference'  => $entity->payment_reference,
            'note'               => $entity->note,
        ]);

        $context->getRequest()->setData($data->toArray());

        return $result;
    }

    /**
     * Validate payment
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateVerify(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($entities as $entity)
            {
                // Check order status if it can be verified
                if ($entity->order_status <> 'awaiting_verification') {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Awaiting Verification" status can be verified'));
                    $result = false;
                }

                // Check inventory for available stock
                $package_id = (int) trim($entity->package_id);
                $package    = $this->getObject('com:nucleonplus.model.packages')->id($package_id)->fetch();
                foreach ($package->getItems() as $item)
                {
                    if (!$item->hasAvailableStock())
                    {
                        throw new KControllerExceptionRequestInvalid($translator->translate("Insufficient stock of {$item->_item_name}"));
                        $result = false;
                    }
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        return $result;
    }

    /**
     * Validate void action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateVoid(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($entities as $entity)
            {
                if (!in_array($entity->order_status, array('awaiting_payment', 'awaiting_verification'))) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Awaiting Payment" or "Awaiting Verfication" status can be voided'));
                    $result = false;
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        return $result;
    }

    /**
     * Validate delivered action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateDelivered(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($entities as $entity)
            {
                if ($entity->order_status <> 'shipped') {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Shipped" status can be marked as "Delivered"'));
                    $result = false;
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        return $result;
    }

    /**
     * Validate completed action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateCompleted(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($entities as $entity)
            {
                if ($entity->order_status <> 'delivered') {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Delivered" status can be marked as "Completed"'));
                    $result = false;
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }

        return $result;
    }

    /**
     * Create Order
     *
     * @param KControllerContextInterface $context
     *
     * @return entity
     */
    protected function _actionAdd(KControllerContextInterface $context)
    {
        $order = parent::_actionAdd($context);

        // Create reward
        $this->getObject($this->_reward)->create($order);

        if ($order->invoice_status == 'paid')
        {
            try
            {
                // Fetch the newly created Order from the data store to get the joined columns
                $order = $this->getObject('com:nucleonplus.model.orders')->id($order->id)->fetch();
                $this->_salesreceipt_service->recordSale($order);
                $context->response->addMessage("Order #{$order->id} has been created and paid");

                // Automatically activate reward
                $reward = $this->_activateReward($order);
                $context->response->addMessage("Reward #{$reward->id} has been activated");
            }
            catch (Exception $e)
            {
                $context->response->addMessage($e->getMessage(), 'exception');
            }
        }
        
        return $order;
    }

    /**
     * Disallow direct editing
     *
     * @param KControllerContextInterface $context
     *
     * @throws KControllerExceptionRequestNotAllowed
     *
     * @return void
     */
    protected function _actionEdit(KControllerContextInterface $context)
    {
        throw new KControllerExceptionRequestNotAllowed('Direct editing of order is not allowed');
    }

    /**
     * Specialized save action, changing state by marking as paid
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionVerifypayment(KControllerContextInterface $context)
    {
        // Mark as Paid
        $context->getRequest()->setData([
            'invoice_status'    => 'paid',
            'order_status'      => 'processing',
            'payment_reference' => $context->request->data->payment_reference,
            'note'              => $context->request->data->note,
        ]);

        $orders = parent::_actionEdit($context);

        foreach ($orders as $order)
        {
            if ($order->invoice_status == 'paid')
            {
                $order = $this->getModel()->id($order->id)->fetch();
                $this->_salesreceipt_service->recordSale($order);
                $context->response->addMessage("Payment for Order #{$order->id} has been verified");

                // Automatically activate reward
                $this->activatereward($context);
            }
        }
        
        return $orders;
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionShip(KControllerContextInterface $context)
    {
        $context->getRequest()->setData([
            'order_status'       => 'shipped',
            'tracking_reference' => $context->request->data->tracking_reference,
        ]);

        return parent::_actionEdit($context);
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionMarkdelivered(KControllerContextInterface $context)
    {
        $context->getRequest()->setData([
            'order_status' => 'delivered'
        ]);

        return parent::_actionEdit($context);
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionMarkcompleted(KControllerContextInterface $context)
    {
        $context->getRequest()->setData([
            'order_status' => 'completed'
        ]);

        return parent::_actionEdit($context);
    }

    /**
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionVoid(KControllerContextInterface $context)
    {
        // Mark as Paid
        $context->getRequest()->setData([
            'order_status' => 'void',
            'note'         => $context->request->data->note,
        ]);

        return parent::_actionEdit($context);
    }

    /**
     * Activates the reward and create corresponding slots
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionActivatereward(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $orders = $this->getModel()->fetch();
        } else {
            $orders = $context->result;
        }

        if (count($orders))
        {
            try
            {
                foreach ($orders as $order) {
                    $reward = $this->_activateReward($order);
                    $context->response->addMessage("Reward #{$reward->id} has been activated");
                }
            }
            catch (Exception $e)
            {
                $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'exception');
                $context->getResponse()->send();
            }
        }
        else throw new KControllerExceptionResourceNotFound('Resource could not be found');

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
     * @return  KModelEntityInterface
     */
    protected function _activateReward(KModelEntityInterface $order)
    {
        $translator = $this->getObject('translator');

        // Check order status if its reward can be activated
        if (!in_array($order->order_status, array('processing', 'completed'))) {
            throw new KControllerExceptionRequestInvalid($translator->translate("Unable to activate corresponding reward: Order #{$order->id} should be in \"Processing\" status"));
        }

        // Try to activate reward
        $reward = $this->getObject('com:nucleonplus.model.rewards')->product_id($order->id)->fetch();
        $this->getObject('com:nucleonplus.controller.reward')->id($reward->id)->activate();

        return $reward;
    }
}