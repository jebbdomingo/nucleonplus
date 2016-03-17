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
     * Inventory Service
     *
     * @var ComNucleonplusAccountingServiceInventory
     */
    protected $_inventory_service;

    /**
     * Journal Service
     *
     * @var ComNucleonplusAccountingServiceJournalInterface
     */
    protected $_journal_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Inventory service
        $identifier       = $this->getIdentifier($config->inventory_service);
        $inventoryService = $this->getObject($identifier);

        if (!($inventoryService instanceof ComNucleonplusAccountingServiceInventory))
        {
            throw new UnexpectedValueException(
                "Inventory Service $identifier does not implement ComNucleonplusAccountingServiceInventory"
            );
        }
        else $this->_inventory_service = $inventoryService;

        // Journal service
        $identifier     = $this->getIdentifier($config->journal_service);
        $journalService = $this->getObject($identifier);

        if (!($journalService instanceof ComNucleonplusAccountingServiceJournalInterface))
        {
            throw new UnexpectedValueException(
                "Journal Service $identifier does not implement ComNucleonplusAccountingServiceJournalInterface"
            );
        }
        else $this->_journal_service = $journalService;
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
            'inventory_service' => 'com:nucleonplus.accounting.service.inventory',
            'journal_service'   => 'com:nucleonplus.accounting.service.journal',
        ));

        parent::_initialize($config);
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

        $data = new KObjectConfig([
            'package_name'       => $package->name,
            'package_price'      => $package->price,
            'account_id'         => $context->request->data->account_id,
            'package_id'         => $context->request->data->package_id,
            'tracking_reference' => $context->request->data->tracking_reference,
            'payment_reference'  => $context->request->data->payment_reference,
            'note'               => $context->request->data->note,
        ]);

        switch ($context->request->data->form_type)
        {
             case 'pos':
                $data->merge([
                    'order_status'    => 'completed',
                    'invoice_status'  => 'paid',
                    'payment_method'  => 'cash',
                    'shipping_method' => 'na',
                ]);
                break;
             
             default:
                $data->merge([
                    'order_status'    => 'awaiting_payment',
                    'invoice_status'  => 'sent',
                    'payment_method'  => 'deposit',
                    'shipping_method' => 'xend',
                ]);
                break;
         }

        $context->getRequest()->setData($data->toArray());

        $order = parent::_actionAdd($context);

        // Record sale and update inventory
        if ($order->invoice_status == 'paid')
        {
            // TODO implement a local queue of accounting/inventory transactions in case of trouble connecting to accounting system
            $this->_journal_service->recordSale($order);
            $this->_inventory_service->decreaseQuantity($order);
        }

        return $order;
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
     * Process Members Rebates
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    /*protected function _actionProcessrewards(KControllerContextInterface $context)
    {
        $rewards = $this->getObject('com:nucleonplus.model.rewards')->fetch();

        foreach ($rewards as $reward) {
            $reward->processRebate();
        }
    }*/
}