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
     * @var integer
     */
    protected $_department_ref;

    /**
     *
     * @var integer
     */
    protected $_bank_account_ref;

    /**
     *
     * @var integer
     */
    protected $_undeposited_account_ref;

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

        $this->_salesreceipt            = $this->getObject($config->salesreceipt_controller);
        $this->_salesreceipt_line       = $this->getObject($config->salesreceipt_line_controller);
        $this->_item_controller         = $this->getObject($config->item_controller);
        $this->_department_ref          = $config->department_ref;
        $this->_bank_account_ref        = $config->bank_account_ref;
        $this->_undeposited_account_ref = $config->undeposited_account_ref;

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
        $data = $this->getObject('com:nucleonplus.accounting.service.data');

        $config->append(array(
            'salesreceipt_controller'      => 'com:qbsync.controller.salesreceipt',
            'salesreceipt_line_controller' => 'com:qbsync.controller.salesreceiptline',
            'item_controller'              => 'com:qbsync.controller.item',
            'transfer_service'             => 'com:nucleonplus.accounting.service.transfer',
            'department_ref'               => $data->store_angono,
            'bank_account_ref'             => $data->account_bank_ref, // Bank Account
            'undeposited_account_ref'      => $data->account_undeposited_ref, // Undeposited Funds Account
            'system_fee_rate'              => $data->rate_system_fee,
            'contingency_fund_rate'        => $data->rate_contingency_fund,
            'operating_expense_rate'       => $data->rate_operating_expense,
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
            'DocNumber'    => $order->id,
            'TxnDate'      => date('Y-m-d'),
            'CustomerRef'  => $order->_account_customer_ref,
            'CustomerMemo' => 'Thank you for your business and have a great day!',
        );

        if ($order->payment_method == 'deposit')
        {
            $salesReceiptData['DepartmentRef']       = $this->_department_ref; // Angono EC Valle store
            $salesReceiptData['DepositToAccountRef'] = $this->_bank_account_ref; // Bank Account
        }
        else
        {
            $user     = $this->getObject('user');
            $employee = $this->getObject('com:nucleonplus.model.employeeaccounts')->user_id($user->getId())->fetch();
            
            $salesReceiptData['DepartmentRef']       = $employee->DepartmentRef; // Store branch
            $salesReceiptData['DepositToAccountRef'] = $this->_undeposited_account_ref; // Undeposited Funds Account
        }

        $salesReceipt = $this->_salesreceipt->add($salesReceiptData);

        // Product line items
        foreach ($order->getItems() as $item)
        {
            $this->_salesreceipt_line->add(array(
                'SalesReceipt' => $salesReceipt->id,
                'Description'  => $item->_item_name,
                'ItemRef'      => $item->_syncitem_item_ref,
                'Qty'          => $item->quantity,
                'Amount'       => ($item->_syncitem_unit_price * $item->quantity),
            ));

            // Update item's quantity purchased for real time inventory quantity tracking
            $syncItem = $this->getObject('com:qbsync.model.items')->item_id($item->item_id)->fetch();
            $syncItem->updateQuantityPurchased($item->quantity);
            $syncItem->save();
        }

        // Service line items
        $package = $order->getPackage();
        if ($order->shipping_method == 'xend')
        {
            $this->_salesreceipt_line->add(array(
                'SalesReceipt' => $salesReceipt->id,
                'Description'  => "{$order->package_name} + Delivery Charge",
                'ItemRef'      => $package->_qbopackage_itemref2,
                'Qty'          => 1,
                'Amount'       => $package->_qbopackage_unitprice2,
            ));

            // Delivery charge
            $deliveryExpense = $order->getPackage()->delivery_charge;

            if ($order->payment_method == 'deposit') {
                $this->_transfer_service->allocateDeliveryExpense($order->id, $deliveryExpense);
            }
        }
        else
        {
            $this->_salesreceipt_line->add(array(
                'SalesReceipt' => $salesReceipt->id,
                'Description'  => "{$order->package_name} Service",
                'ItemRef'      => $package->_qbopackage_itemref,
                'Qty'          => 1,
                'Amount'       => $package->_qbopackage_unitprice,
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