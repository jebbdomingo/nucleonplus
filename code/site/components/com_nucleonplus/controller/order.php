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
     * @var ComNucleonplusRebatePackagereward
     */
    protected $_reward;

    /**
     * Reward
     *
     * @var string
     */
    protected $_nucleonplus_bank_account_number;

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
        $this->_nucleonplus_bank_account_number = $config->nucleonplus_bank_account_number;

        // Validation
        $this->addCommandCallback('before.add', '_validate');
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
            'reward'                          => 'com://admin/nucleonplus.rebate.packagereward',
            'nucleonplus_bank_account_number' => '9900000001',
        ));

        parent::_initialize($config);
    }

    /**
     * Validate and construct data
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validate(KControllerContextInterface $context)
    {
        $result = true;

        try
        {
            $user       = $this->getObject('user');
            $translator = $this->getObject('translator');
            
            if ($this->getModel('com:nucleonplus.model.orders')->hasCurrentOrder($user->getId())) {
                throw new KControllerExceptionRequestInvalid($translator->translate('You can only purchase one product package per day'));
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
        $package = $this->getObject('com:nucleonplus.model.packages')->id($context->request->data->package_id)->fetch();

        $context->getRequest()->setData([
            // Copy the package data in the order table
            'package_name'       => $package->name,
            'package_price'      => $package->price,

            'account_id'         => $context->request->data->account_id,
            'package_id'         => $context->request->data->package_id,
            'order_status'       => 'awaiting_payment',
            'invoice_status'     => 'sent',
            'payment_method'     => 'deposit',
            'shipping_method'    => 'xend',
            'tracking_reference' => $context->request->data->tracking_reference,
            'payment_reference'  => $context->request->data->payment_reference,
            'note'               => $context->request->data->note,
        ]);

        $order = parent::_actionAdd($context);

        $response = $context->getResponse();
        $response->addMessage("Please deposit your payment to BDO account # {$this->_nucleonplus_bank_account_number} and enter the reference number found in your deposit slip to \"Deposit slip reference #\" field in your <a href=\"component/nucleonplus/?view=order&id={$order->id}&layout=form&tmpl=koowa\">Order #{$order->id}</a>.");

        // Create reward
        $this->_reward->create($order);

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
            'order_status'      => 'verifying',
            'payment_reference' => $context->getRequest()->data->payment_reference
        ]);


        $order = parent::_actionEdit($context);

        $response = $context->getResponse();
        $response->addMessage('Thank you for your payment, we will ship your order immediately once your payment has been verified.');

        $identifier = $context->getSubject()->getIdentifier();
        $view       = KStringInflector::singularize($identifier->name);
        $url        = sprintf('index.php?option=com_%s&view=%s&layout=form&tmpl=koowa&id=%d', $identifier->package, $view, $order->id);

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

        return parent::_actionEdit($context);
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
        $response->addMessage('Your order has been cancelled.');

        $identifier = $context->getSubject()->getIdentifier();
        $view       = KStringInflector::singularize($identifier->name);
        $url        = sprintf('index.php?option=com_%s&view=%s&layout=form&tmpl=koowa&id=%d', $identifier->package, $view, $order->id);

        $response->setRedirect(JRoute::_($url, false));
    }
}