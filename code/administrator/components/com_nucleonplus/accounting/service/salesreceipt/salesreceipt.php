<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * @todo Implement a local queue of accounting/inventory transactions in case of trouble connecting to accounting system
 */
class ComNucleonplusAccountingServiceSalesreceipt extends KObject implements ComNucleonplusAccountingServiceSalesreceiptInterface
{
    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_salesreceipt;

    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_salesreceipt_line;

    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_item_controller;

    /**
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_transfer_service;

    /**
     *
     * @var decimal
     */
    protected $_system_fee_rate;

    /**
     *
     * @var decimal
     */
    protected $_contingency_fund_rate;

    /**
     *
     * @var decimal
     */
    protected $_operating_expense_rate;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_salesreceipt      = $this->getObject($config->salesreceipt_controller);
        $this->_salesreceipt_line = $this->getObject($config->salesreceipt_line_controller);
        $this->_item_controller   = $this->getObject($config->item_controller);

        // Transfer service
        $identifier = $this->getIdentifier($config->transfer_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceTransferInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceTransferInterface"
            );
        }
        else $this->_transfer_service = $service;

        $this->_system_fee_rate        = $config->system_fee_rate;
        $this->_contingency_fund_rate  = $config->contingency_fund_rate;
        $this->_operating_expense_rate = $config->operating_expense_rate;
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
            'salesreceipt_controller'      => 'com:qbsync.controller.salesreceipt',
            'salesreceipt_line_controller' => 'com:qbsync.controller.salesreceiptline',
            'item_controller'              => 'com:qbsync.controller.item',
            'transfer_service'             => 'com:nucleonplus.accounting.service.transfer',
            'system_fee_rate'              => 10.00,
            'contingency_fund_rate'        => 50.00,
            'operating_expense_rate'       => 60.00,
        ));

        parent::_initialize($config);
    }

    /**
     * Record sales transaction in the accounting system 
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    public function recordSale(KModelEntityInterface $order)
    {
        // Create sales receipt sync queue
        $salesReceiptData = array(
            'DocNumber'   => $order->id,
            'TxnDate'     => date('Y-m-d'),
            'CustomerRef' => $order->_account_customer_ref,
        );
        $salesReceipt = $this->_salesreceipt->add($salesReceiptData);

        // Create corresponding sales receipt line items
        foreach ($order->getItems() as $item)
        {
            $inventoryItem = $this->_item_controller
                ->id($item->inventory_item_id)
                ->getModel()
                ->fetch()
            ;

            $this->_salesreceipt_line->add(array(
                'SalesReceipt' => $salesReceipt->id,
                'Description'  => $item->name,
                'ItemRef'      => QuickBooks_IPP_IDS::usableIDType($inventoryItem->getId()),
                'Qty'          => $item->quantity,
                'Amount'       => ($inventoryItem->getUnitPrice() * $item->quantity),
            ));
        }

        // Create package item
        if ($order->shipping_method == 'xend')
        {
            // Product package + delivery service charge
            $serviceItem = $this->_item_controller
                ->id($order->getPackage()->acctg_item_delivery_id)
                ->getModel()
                ->fetch()
            ;
            $this->_salesreceipt_line->add(array(
                'SalesReceipt' => $salesReceipt->id,
                'Description'  => "{$order->package_name} + Delivery Charge",
                'ItemRef'      => QuickBooks_IPP_IDS::usableIDType($serviceItem->getId()),
                'Qty'          => 1,
                'Amount'       => $serviceItem->getUnitPrice(),
            ));

            // Delivery charge
            $deliveryExpense = $order->getPackage()->delivery_charge;

            if ($order->payment_method == 'deposit') {
                $this->_transfer_service->allocateDeliveryExpense($order->id, $deliveryExpense);
            }
        }
        else
        {
            // Product package
            $serviceItem = $this->_item_controller
                ->id($order->getPackage()->acctg_item_id)
                ->getModel()
                ->fetch()
            ;
            $this->_salesreceipt_line->add(array(
                'SalesReceipt' => $salesReceipt->id,
                'Description'  => "{$order->package_name} Service",
                'ItemRef'      => QuickBooks_IPP_IDS::usableIDType($serviceItem->getId()),
                'Qty'          => 1,
                'Amount'       => $serviceItem->getUnitPrice(),
            ));
        }

        // Allocation parts of sale
        $systemFee        = ($this->_system_fee_rate * $order->getReward()->slots);
        $contingencyFund  = ($this->_contingency_fund_rate * $order->getReward()->slots);
        $operatingExpense = ($this->_operating_expense_rate * $order->getReward()->slots);

        $this->_transfer_service->allocateSystemFee($order->id, $systemFee);
        $this->_transfer_service->allocateContingencyFund($order->id, $contingencyFund);
        $this->_transfer_service->allocateOperationsFund($order->id, $operatingExpense);
    }
}