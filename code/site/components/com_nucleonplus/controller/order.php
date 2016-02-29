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
     * Create Order
     *
     * @param KControllerContextInterface $context
     *
     * @return entity
     */
    protected function _actionAdd(KControllerContextInterface $context)
    {
        $package = $this->getObject('com:nucleonplus.model.packages')->id($context->request->data->package_id)->fetch();

        // Copy the package data in the order table
        $context->request->data->package_name  = $package->name;
        $context->request->data->package_price = $package->price;
        $context->request->data->order_status   = 'awaiting_payment';
        $context->request->data->invoice_status = 'sent';

        return parent::_actionAdd($context);
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