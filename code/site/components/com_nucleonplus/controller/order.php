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

    /**
     * Validate add
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    // protected function _validate(KControllerContextInterface $context)
    // {
    //     if(!$context->result instanceof KModelEntityInterface) {
    //         $entity = $this->getModel()->create($context->request->data->toArray());
    //     } else {
    //         $entity = $context->result;
    //     }

    //     $result = true;

    //     try
    //     {
    //         $user       = $this->getObject('user');
    //         $translator = $this->getObject('translator');
    //         $package_id = (int) trim($entity->package_id);
    //         $package    = $this->getObject('com:nucleonplus.model.packages')->id($package_id)->fetch();
            
    //         // Validate account
    //         $account = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();

    //         if (count($account) === 0)
    //         {
    //             throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Account'));
    //             $result = false;
    //         }
            
    //         if (empty(trim($entity->package_id)))
    //         {
    //             throw new KControllerExceptionRequestInvalid($translator->translate('Please select a Product Pack'));
    //             $result = false;
    //         }
    //         elseif (count($package) === 0)
    //         {
    //             throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Product Pack'));
    //             $result = false;
    //         }

    //         // Check inventory for available stock
    //         foreach ($package->getItems() as $item)
    //         {
    //             if (!$item->hasAvailableStock())
    //             {
    //                 throw new KControllerExceptionActionFailed($translator->translate("Insufficient stock of {$item->_item_name}"));
    //                 $result = false;
    //             }
    //         }
    //     }
    //     catch(Exception $e)
    //     {
    //         $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
    //         $context->getResponse()->send();

    //         $result = false;
    //     }

    //     $data = new KObjectConfig([
    //         'account_id'      => $account->id,
    //         'package_id'      => $package->id,
    //         'package_name'    => $package->name,
    //         'package_price'   => $package->price,
    //         'order_status'    => 'awaiting_payment',
    //         'invoice_status'  => 'sent',
    //         'payment_method'  => 'deposit',
    //         'shipping_method' => 'xend',
    //     ]);

    //     $context->getRequest()->setData($data->toArray());

    //     return $result;
    // }

    protected function _validate(KControllerContextInterface $context)
    {
        $user       = $this->getObject('user');
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $translator = $this->getObject('translator');
        $data       = $context->request->data;
        $error      = false;

        // Validate account
        if (count($account) === 0)
        {
            $error = 'Invalid Account';
        }
        else
        {
            if (empty(trim($data->address))) {
                $error = 'Invalid address';
            }

            if (empty(trim($data->city))) {
                $error = 'Invalid city';
            }

            if (empty(trim($data->state_province))) {
                $error = 'Invalid state/province';
            }

            if (!in_array($data->region, array('metro_manila', 'luzon', 'visayas', 'mindanao'))) {
                $error = 'Invalid region';
            }

            foreach ($data->quantity as $id => $qty)
            {
                $cartItem = $this->getObject('com://admin/nucleonplus.model.carts')->id($id)->fetch();
                $package  = $this->getObject('com:nucleonplus.model.packages')->id($cartItem->package_id)->fetch();

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
    // protected function _actionAdd(KControllerContextInterface $context)
    // {
    //     try
    //     {
    //         $order    = parent::_actionAdd($context);
    //         $response = $context->getResponse();

    //         $response->addMessage("Thank you for your business, we will process your order once you confirm your payment. Please see the instruction below.");

    //         $paymentInstruction = $context->getSubject()->getView()->getTemplate()->invoke('alerts.paymentInstructionMessage');
    //         $response->addMessage($paymentInstruction, 'info');

    //         // Create reward
    //         $this->_reward->create($order);

    //         $identifier = $context->getSubject()->getIdentifier();
    //         $url        = sprintf('index.php?option=com_%s&view=orders', $identifier->package);
    //         $response->setRedirect(JRoute::_($url, false));
    //     }
    //     catch(Exception $e)
    //     {
    //         $context->response->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');

    //         if (!$context->result instanceof KModelEntityInterface) {
    //             $order = $this->getModel()->fetch();
    //         } else {
    //             $order = $context->result;
    //         }
    //     }

    //     return $order;
    // }

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
        $response   = $context->getResponse();
        $data       = $context->request->data;

        $order = $this->getModel()->create(array(
            'account_id'      => $account->id,
            'order_status'    => 'awaiting_payment',
            'invoice_status'  => 'sent',
            'payment_method'  => 'deposit',
            'shipping_method' => 'xend',
            'address'         => $data->address,
            'city'            => $data->city,
            'state_province'  => $data->state_province,
            'region'          => $data->region,
        ));

        try
        {
            $order->save();

            foreach ($data->quantity as $id => $qty)
            {
                $cartItem = $this->getObject('com://admin/nucleonplus.model.carts')->id($id)->fetch();
                $package  = $this->getObject('com:nucleonplus.model.packages')->id($cartItem->package_id)->fetch();

                $orderItem = $this->getObject('com://admin/nucleonplus.model.orderitems')->create(array(
                    'order_id'      => $order->id,
                    'package_id'    => $package->id,
                    'package_name'  => $package->name,
                    'package_price' => $package->price,
                    'quantity'      => $qty,
                ));
                $orderItem->save();

                // Create reward
                $this->_reward->create($orderItem);

                // Delete the item in the cart
                $cartItem->delete();
            }

            // $response->addMessage("Thank you for your business, we will process your order once your payment has been confirmed.");

            // $paymentInstruction = $context->getSubject()->getView()->getTemplate()->invoke('alerts.paymentInstructionMessage');
            // $response->addMessage($paymentInstruction, 'info');

            // $identifier = $context->getSubject()->getIdentifier();
            // $url        = sprintf('index.php?option=com_%s&view=orders', $identifier->package);
            // $response->setRedirect(JRoute::_($url, false));




            // $merchant = 'NUCLEON';
            // $passwd   = 'eRGTsJ73DcjkL2J';

            // $parameters = array(
            //     'merchantid'  => $merchant,
            //     'txnid'       => $order->id,
            //     'amount'      => number_format($order->getSubTotal(), 2, '.', ''),
            //     'ccy'         => 'PHP',
            //     'description' => 'Order description.',
            //     'email'       => $user->getEmail(),
            // );

            // $parameters['key'] = $passwd;
            // $digest_string = implode(':', $parameters);
            // unset($parameters['key']);

            // $parameters['digest'] = sha1($digest_string);

            // $url = 'http://test.dragonpay.ph/Pay.aspx?';
            // $url .= http_build_query($parameters, '', '&');

            // $response->setRedirect(JRoute::_($url, false));
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