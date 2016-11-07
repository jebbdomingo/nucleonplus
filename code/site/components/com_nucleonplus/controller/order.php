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
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Reward service
        $this->_reward = $this->getObject($config->reward);

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
            'reward'    => 'com://admin/nucleonplus.mlm.packagereward',
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
            if (count($cart))
            {
                if (empty(trim($cart->address))) {
                    $error = 'Invalid address';
                }

                if (empty(trim($cart->city_id))) {
                    $error = 'Invalid city';
                }

                foreach ($cart->getItems() as $item)
                {
                    // Check inventory for available stock
                    if (!$item->hasAvailableStock()) {
                        $error = "Insufficient stock of {$item->_item_name}";
                    }
                }
            }
            else $error = 'Cart System Error';
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
            'order_status'    => ComNucleonplusModelEntityOrder::STATUS_VERIFICATION,
            'invoice_status'  => ComNucleonplusModelEntityOrder::INVOICE_STATUS_SENT,
            'payment_method'  => ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY,
            'shipping_method' => ComNucleonplusModelEntityOrder::SHIPPING_METHOD_XEND,
            'address'         => $cart->address,
            'city_id'         => $cart->city_id,
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
                    $orderItem = $this->getObject('com://admin/nucleonplus.model.orderitems')->create(array(
                        'order_id'   => $order->id,
                        'ItemRef'    => $item->_item_ref,
                        'item_name'  => $item->_item_name,
                        'item_price' => $item->_item_price,
                        'quantity'   => $item->quantity,
                    ));
                    $orderItem->save();

                    // Create reward
                    for ($i=0; $i < $orderItem->quantity; $i++) { 
                        $this->_reward->create($orderItem);
                    }
                }

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