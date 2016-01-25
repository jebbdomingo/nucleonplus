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
     * @todo This can be turned into a controller behavior if deemed necessary
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

        $entity = parent::_actionAdd($context);

        // Redirect
        $identifier = $context->getSubject()->getIdentifier();
        $view       = KStringInflector::singularize($identifier->name);
        $url        = sprintf('index.php?option=com_%s&view=%s', $identifier->package, $view);

        $context->response->setRedirect($this->getObject('lib:http.url',array('url' => $url)));

        return $entity;
    }

    /**
     * Specialized save action, changing state by marking as paid
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionMarkpaid(KControllerContextInterface $context)
    {
        // Mark as Paid
        $context->request->data->add(['invoice_status' => 'paid']);

        return parent::_actionEdit($context);
    }

    /**
     * Process Members Rebates
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _actionProcessrebates(KControllerContextInterface $context)
    {
        $rebates = $this->getObject('com:nucleonplus.model.rebates')->fetch();

        foreach ($rebates as $rebate) {
            $rebate->processRebate();
        }
    }
}