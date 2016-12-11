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
     * Inventory service
     *
     * @var ComNucleonplusAccountingInventoryQuantityInterface
     */
    protected $_inventory_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        @ini_set('max_execution_time', 300);
        
        parent::__construct($config);

        // Reward service
        $this->_reward = $this->getObject($config->reward);

        // Validation
        $this->addCommandCallback('before.add', '_validate');
        $this->addCommandCallback('before.cancelorder', '_validateCancelorder');

        // Inventory service
        $identifier = $this->getIdentifier($config->inventory_service);
        $service    = $this->getObject($identifier);
        if (!($service instanceof ComNucleonplusAccountingServiceInventoryInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceInventoryInterface"
            );
        }
        else $this->_inventory_service = $service;
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
            'reward'            => 'com://admin/nucleonplus.mlm.packagereward',
            'inventory_service' => 'com://admin/nucleonplus.accounting.service.inventory',
            'behaviors'         => array(
                // 'onlinepayable',
                'com://admin/nucleonplus.controller.behavior.cancellable',
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
            if (count($cart))
            {
                if (empty(trim($cart->address))) {
                    $error = 'Invalid address';
                }

                if (empty(trim($cart->city_id))) {
                    $error = 'Invalid city';
                }

                $itemQty = $cart->getItemQuantities();

                foreach ($itemQty as $id => $qty)
                {
                    $result = $this->_inventory_service->getQuantity($id, true);

                    if ($result['available'] < $qty)
                    {
                        $error  = "Insufficient stock of {$result['Name']}, only ({$result['available']}) item/s left in stock and you already have ({$qty}) in your shopping cart";
                        
                        if (JDEBUG)
                        {
                            $error .= '<pre>' . print_r($itemQty, true) . '</pre>';
                            $error .= '<pre>' . print_r($result, true) . '</pre>';
                        }
                    }
                }
            }
            else $error = 'Cart System Error - Invalid Shopping Cart';
        }

        if ($error)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $translator->translate($error), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Validate cancellation of order
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateCancelorder(KControllerContextInterface $context)
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

                if (!in_array($order->order_status, array(ComNucleonplusModelEntityOrder::STATUS_PAYMENT, ComNucleonplusModelEntityOrder::STATUS_PENDING))) {
                    throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Order Status: Only Order(s) with "Pending" or "Awaiting Payment" status can be cancelled'));
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
            'order_status'    => ComNucleonplusModelEntityOrder::STATUS_PENDING,
            'invoice_status'  => ComNucleonplusModelEntityOrder::INVOICE_STATUS_SENT,
            'payment_method'  => ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY,
            'shipping_method' => ComNucleonplusModelEntityOrder::SHIPPING_METHOD_XEND,
            'recipient_name'  => $cart->recipient_name,
            'address'         => $cart->address,
            'city'            => $cart->city_id,
            'postal_code'     => $cart->postal_code,
            'shipping_cost'   => $cart->getShippingFee(),
            'payment_charge'  => $cart->getPaymentCharge(),
            'payment_mode'    => $cart->payment_mode,
        ));

        try
        {
            if ($order->save())
            {
                foreach ($cart->getItems() as $item)
                {
                    $orderItem = $this->getObject('com://admin/nucleonplus.model.orderitems')->create(array(
                        'order_id'   => $order->id,
                        'ItemRef'    => $item->_item_ref,
                        'item_name'  => $item->_item_name,
                        'item_price' => $item->_item_price,
                        'item_image' => $item->_item_image,
                        'quantity'   => $item->quantity,
                    ));
                    $orderItem->save();

                    // Create reward
                    for ($i=0; $i < $orderItem->quantity; $i++) { 
                        $this->_reward->create($orderItem);
                    }
                }

                // Calculate order totals based on order items
                $order
                    ->calculate()
                    ->save()
                ;

                /**
                 * @todo Move to com:cart behavior
                 */
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
     * Specialized save action, changing state by updating the order status
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionMarkdelivered(KControllerContextInterface $context)
    {
        $this->getObject('com://admin/nucleonplus.model.orders');

        $context->getRequest()->setData([
            'order_status' => ComNucleonplusModelEntityOrder::STATUS_DELIVERED
        ]);

        $order = parent::_actionEdit($context);

        $context->response->addMessage('Thank you for your business');

        return $order;
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
        $context->getRequest()->setData([
            'order_status' => ComNucleonplusModelEntityOrder::STATUS_CANCELLED
        ]);

        $order = parent::_actionEdit($context);

        $context->response->addMessage("Your Order #{$order->id} has been cancelled.");

        return $order;
    }
}