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
        $this->addCommandCallback('before.ship', '_validateShip');
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
            'reward'               => 'com:nucleonplus.mlm.packagereward',
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
        $translator = $this->getObject('translator');
        $data       = $context->request->data;
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($data->account_id)->fetch();
        $cart       = $this->getObject('com://admin/nucleonplus.model.carts')->id($data->cart_id)->fetch();
        $error      = false;

        // Validate account
        if (count($account) === 0)
        {
            $error = 'Invalid Account';
        }
        else
        {
            if (count($cart->getItems()))
            {
                foreach ($cart->getItems() as $item)
                {
                    // Check inventory for available stock
                    if (!$item->hasAvailableStock()) {
                        $error = "Insufficient stock of {$item->_item_name}";
                    }
                }
            }
            else throw new Exception('Shopping cart is empty');
        }

        if ($error)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $translator->translate($error), 'error');
            $context->getResponse()->send();
        }

        $order_status    = 'completed';
        $invoice_status  = 'paid';
        $payment_method  = 'cash';
        $shipping_method = 'na';

        $data = new KObjectConfig([
            'account_id'      => $account->id,
            'order_status'    => $order_status,
            'invoice_status'  => $invoice_status,
            'payment_method'  => $payment_method,
            'shipping_method' => $shipping_method
        ]);

        $context->getRequest()->setData($data->toArray());
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
                foreach ($entity->getOrderItems() as $item)
                {
                    $package  = $this->getObject('com:nucleonplus.model.packages')->id($item->package_id)->fetch();

                    if (count($package) === 0)
                    {
                        throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Product Pack'));
                        $result = false;
                    }

                    // Check inventory for available stock
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
     * Validate ship action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateShip(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $orders = $this->getModel()->fetch();
        } else {
            $orders = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($orders as $order)
            {
                $order->setProperties($context->request->data->toArray());

                if ($order->order_status <> 'processing') {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Processing" status can be shipped'));
                }

                if (empty(trim($order->tracking_reference))) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Please enter shipment tracking reference'));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
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
    // protected function _actionAdd(KControllerContextInterface $context)
    // {
    //     $order = parent::_actionAdd($context);

    //     // Create reward
    //     $this->getObject($this->_reward)->create($order);

    //     if ($order->invoice_status == 'paid')
    //     {
    //         try
    //         {
    //             // Fetch the newly created Order from the data store to get the joined columns
    //             $order = $this->getObject('com:nucleonplus.model.orders')->id($order->id)->fetch();
    //             $this->_salesreceipt_service->recordSale($order);
    //             $context->response->addMessage("Order #{$order->id} has been created and paid");

    //             // Automatically activate reward
    //             $this->_activateReward($order);
    //         }
    //         catch (Exception $e)
    //         {
    //             $context->response->addMessage($e->getMessage(), 'exception');
    //         }
    //     }
        
    //     return $order;
    // }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $user       = $this->getObject('user');
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $translator = $this->getObject('translator');
        $data       = $context->request->data;
        $cart       = $this->getObject('com://admin/nucleonplus.model.carts')->id($data->cart_id)->fetch();

        $order = $this->getModel()->create(array(
            'account_id'      => $data->account_id,
            'order_status'    => $data->order_status,
            'invoice_status'  => $data->invoice_status,
            'payment_method'  => $data->payment_method,
            'shipping_method' => $data->shipping_method,
            'address'         => $cart->address,
            'city_id'         => $cart->city_id,
            'postal_code'     => $cart->postal_code
        ));

        @ini_set('max_execution_time', 300);

        if ($order->save())
        {
            $rewardPackage = $this->getObject($this->_reward);

            foreach ($cart->getItems() as $item)
            {
                $orderItem = $this->getObject('com://admin/nucleonplus.model.orderitems')->create(array(
                    'order_id'   => $order->id,
                    'ItemRef'    => $item->_item_ref,
                    'item_name'  => $item->_item_name,
                    'item_price' => $item->_item_price,
                    'quantity'   => $item->quantity,
                ));
                $orderItem->save();

                // Create the reward
                for ($i=0; $i < $orderItem->quantity; $i++) { 
                    $rewardPackage->create($orderItem);
                }
            }

            // Delete the cart
            $cart->delete();

            // Record sale in accounting
            $this->_salesreceipt_service->recordSale($order);

            // Automatically activate reward
            $this->_activateReward($order);
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
        $context->getRequest()->data->invoice_status = 'paid';
        $context->getRequest()->data->order_status   = 'processing';

        $orders = parent::_actionEdit($context);

        try
        {
            foreach ($orders as $order)
            {
                $this->_salesreceipt_service->recordSale($order);     
                $context->response->addMessage("Payment for Order #{$order->id} has been verified");

                // Automatically activate reward
                $this->_activateReward($order);
            }

        }
        catch (Exception $e)
        {
            $context->response->addMessage($e->getMessage(), 'exception');
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

        $order = parent::_actionEdit($context);

        // Send email notification
        $config       = JFactory::getConfig();
        $emailSubject = JText::sprintf('COM_NUCLEONPLUS_ORDER_EMAIL_SHIPPED_SUBJECT', $order->id);
        $emailBody    = JText::sprintf(
            'COM_NUCLEONPLUS_ORDER_EMAIL_SHIPPED_BODY',
            $order->name,
            $order->id,
            $order->tracking_reference,
            JUri::root()
        );

        $mail = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $order->_user_email, $emailSubject, $emailBody);
        // Check for an error.
        if ($mail !== true) {
            $context->response->addMessage(JText::_('COM_NUCLEONPLUS_PAYOUT_EMAIL_SEND_MAIL_FAILED'), 'error');
        }

        return $order;
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
                    $this->_activateReward($order);
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
        $rewards = $order->getRewards();
        foreach ($rewards as $reward)
        {
            $this->getObject('com:nucleonplus.controller.reward')->id($reward->id)->activate();
            $this->getResponse()->addMessage("Reward #{$reward->id} has been activated");
        }
    }
}