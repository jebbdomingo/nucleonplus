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
     * Reward
     *
     * @var ComNucleonplusMlmPackagereward
     */
    protected $_reward;

    /**
     * Inventory Service
     *
     * @var ComQbsyncControllerItem
     */
    protected $_item_controller;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Reward service
        $this->_reward = $this->getObject($config->reward);

        // Inventory service
        $this->_item_controller = $this->getObject($config->item_controller);

        // Validation
        $this->addCommandCallback('before.add', '_validate');
        $this->addCommandCallback('before.confirm', '_validateConfirm');
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
            'reward'          => 'com://admin/nucleonplus.mlm.packagereward',
            'item_controller' => 'com:qbsync.controller.item',
            'behaviors' => array(
                'onlinepayable'
            ),
        ));

        parent::_initialize($config);
    }

    protected function _validate(KControllerContextInterface $context)
    {
        $user       = $this->getObject('user');
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $translator = $this->getObject('translator');
        $data       = $context->request->data;
        $cart       = $this->getObject('com://admin/nucleonplus.model.carts')->id($data->cart_id)->fetch();
        $error      = false;

        // Validate account
        if (count($account) === 0)
        {
            $error = 'Invalid Account';
        }
        else
        {
            if (empty(trim($cart->address))) {
                $error = 'Invalid address';
            }

            if (empty(trim($cart->city))) {
                $error = 'Invalid city';
            }

            if (empty(trim($cart->state_province))) {
                $error = 'Invalid state/province';
            }

            if (!in_array($cart->region, array('metro_manila', 'luzon', 'visayas', 'mindanao'))) {
                $error = 'Invalid region';
            }

            foreach ($cart->getItems() as $item)
            {
                $package  = $this->getObject('com:nucleonplus.model.packages')->id($item->package_id)->fetch();

                if (count($package) === 0) {
                    $error = 'Invalid Product Pack';
                }

                // Check inventory for available stock
                foreach ($package->getItems() as $item)
                {
                    if (!$item->hasAvailableStock()) {
                        $error = "Insufficient stock of {$item->_item_name}";
                    }
                }
            }
        }

        if ($error)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $translator->translate($error), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Validate payment confirmation
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateConfirm(KControllerContextInterface $context)
    {
        $result = true;

        try
        {
            $translator = $this->getObject('translator');
            
            if (empty(trim($context->request->data->payment_reference))) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please enter your deposit slip reference #'));
                $result = false;
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
        $user       = $this->getObject('user');
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $translator = $this->getObject('translator');
        $data       = $context->request->data;
        $cart       = $this->getObject('com://admin/nucleonplus.model.carts')->id($data->cart_id)->fetch();

        $order = $this->getModel()->create(array(
            'account_id'      => $account->id,
            'order_status'    => 'awaiting_payment',
            'invoice_status'  => 'sent',
            'payment_method'  => ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY,
            'shipping_method' => 'xend',
            'address'         => $cart->address,
            'city'            => $cart->city,
            'state_province'  => $cart->state_province,
            'region'          => $cart->region,
            'postal_code'     => $cart->postal_code,
            'shipping_cost'   => $cart->getShippingCost(),
            'payment_charge'  => $cart->getPaymentCharge(),
            'payment_mode'    => $cart->payment_mode,
        ));

        try
        {
            if ($order->save())
            {
                foreach ($cart->getItems() as $item)
                {
                    $package  = $this->getObject('com:nucleonplus.model.packages')->id($item->package_id)->fetch();

                    $orderItem = $this->getObject('com://admin/nucleonplus.model.orderitems')->create(array(
                        'order_id'      => $order->id,
                        'package_id'    => $package->id,
                        'package_name'  => $package->name,
                        'package_price' => $package->price,
                        'quantity'      => $item->quantity,
                    ));
                    $orderItem->save();
                }

                // Create reward
                $this->_reward->create($order);

                // Delete the cart
                $cart->delete();
            }
        }
        catch(Exception $e)
        {
            $context->response->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');

            if (!$context->result instanceof KModelEntityInterface) {
                $order = $this->getModel()->fetch();
            } else {
                $order = $context->result;
            }
        }

        return $order;
    }

    /**
     * Special confirm action which wraps edit action
     *
     * @param KControllerContextInterface $context
     *
     * @return entity
     */
    protected function _actionConfirm(KControllerContextInterface $context)
    {
        $context->getRequest()->setData([
            'order_status'      => 'awaiting_verification',
            'payment_reference' => $context->getRequest()->data->payment_reference
        ]);


        $order = parent::_actionEdit($context);

        $response = $context->getResponse();
        $response->addMessage('Thank you for your payment, we will ship your order immediately once your payment has been verified.');

        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=orders', $identifier->package);

        $response->setRedirect(JRoute::_($url, false));
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

        $order = parent::_actionEdit($context);

        $response = $context->getResponse();
        $response->addMessage('Thank you for your business.', 'info');

        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=orders', $identifier->package);

        $response->setRedirect(JRoute::_($url, false));
    }

    /**
     * Cancel Order
     *
     * @param KControllerContextInterface $context
     *
     * @return entity
     */
    protected function _actionCancelorder(KControllerContextInterface $context)
    {
        // Copy the package data in the order table
        $context->request->data->order_status = 'cancelled';

        $order = parent::_actionEdit($context);

        $response = $context->getResponse();
        $response->addMessage('Your order has been cancelled.', 'warning');

        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=orders', $identifier->package);

        $response->setRedirect(JRoute::_($url, false));
    }
}