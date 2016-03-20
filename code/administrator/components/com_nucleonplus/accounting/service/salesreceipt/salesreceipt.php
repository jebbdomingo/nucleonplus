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
class ComNucleonplusAccountingServiceSalesreceipt extends ComNucleonplusAccountingServiceObject implements ComNucleonplusAccountingServiceSalesreceiptInterface
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Inventory service
        $identifier   = $this->getIdentifier($config->inventory_service);
        $inventory_service = $this->getObject($identifier);

        if (!($inventory_service instanceof ComNucleonplusAccountingServiceInventoryInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceInventoryInterface"
            );
        }
        else $this->_inventory_service = $inventory_service;

        // Accounting service
        $identifier         = $this->getIdentifier($config->accounting_service);
        $accounting_service = $this->getObject($identifier);

        if (!($accounting_service instanceof ComNucleonplusAccountingServiceTransferInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceTransferInterface"
            );
        }
        else $this->_accounting_service = $accounting_service;

        // Accounts
        $this->_undeposited_funds_account        = $config->undeposited_funds_account;
        $this->_sales_of_product_account         = $config->sales_of_product_account;
        $this->_system_fee_account               = $config->system_fee_account;
        $this->_contingency_fund_account         = $config->contingency_fund_account;
        $this->_operating_expense_budget_account = $config->operating_expense_budget_account;
        $this->_sales_account                    = $config->sales_account;
        $this->_system_fee_rate                  = $config->system_fee_rate;
        $this->_contingency_fund_rate            = $config->contingency_fund_rate;
        $this->_operating_expense_rate           = $config->operating_expense_rate;
        $this->_rebates_account                  = $config->rebates_account;
        $this->_referral_bonus_account           = $config->referral_bonus_account;
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
            'inventory_service'                => 'com:nucleonplus.accounting.service.inventory',
            'accounting_service'               => 'com:nucleonplus.accounting.service.transfer',
            'undeposited_funds_account'        => 92,
            'system_fee_account'               => 138,
            'contingency_fund_account'         => 139,
            'operating_expense_budget_account' => 140,
            'rebates_account'                  => 141,
            'referral_bonus_account'           => 142,
            'system_fee_rate'                  => 10.00,
            'contingency_fund_rate'            => 50.00,
            'operating_expense_rate'           => 60.00,
        ));

        parent::_initialize($config);
    }

    /**
     * Sales Receipt object
     *
     * @var QuickBooks_IPP_Object_SalesReceipt
     */
    protected $SalesReceipt;

    /**
     * Record sales transaction in the accounting system 
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    public function recordSale(KModelEntityInterface $order)
    {
        $this->_createSalesReceipt($order->id);

        foreach ($order->getItems() as $item)
        {
            $inventoryItem = $this->_inventory_service->find($item->inventory_item_id);

            $this->_createLine(array(
                'description' => $item->name,
                'item_id'     => $inventoryItem->getId(),
                'qty'         => $item->quantity,
                'amount'      => ($inventoryItem->getUnitPrice() * $item->quantity),
            ));
        }

        $serviceItem = $this->_inventory_service->find($order->getPackage()->inventory_service_id);

        $this->_createLine(array(
            'description' => "{$order->package_name} Service",
            'item_id'     => QuickBooks_IPP_IDS::usableIDType($serviceItem->getId()),
            'qty'         => 1,
            'amount'      => $serviceItem->getUnitPrice(),
        ));

        // Record sale
        $this->_save();

        // Allocation parts of sale
        $this->_accounting_service->allocateSystemFee($this->_system_fee_rate);
        $this->_accounting_service->allocateContingencyFund($this->_contingency_fund_rate);
        $this->_accounting_service->allocateOperationsFund($this->_operating_expense_rate);
    }

    /**
     * Create debit line
     *
     * @param array $data
     *
     * @return this
     */
    protected function _createLine(array $data)
    {
        $Line = new QuickBooks_IPP_Object_Line();
        $Line->setDetailType('SalesItemLineDetail');
        $Line->setDescription($data['description']);
        $Line->setAmount($data['amount']);

        $Details = new QuickBooks_IPP_Object_SalesItemLineDetail();
        $Details->setItemRef($data['item_id']);
        $Details->setQty($data['qty']);

        $Line->addSalesItemLineDetail($Details);

        $this->SalesReceipt->addLine($Line);

        return $this;
    }

    /**
     * Create sales receipt object
     *
     * @param string $docNumber
     *
     * @return this
     */
    protected function _createSalesReceipt($docNumber)
    {
        $SalesReceipt = new QuickBooks_IPP_Object_SalesReceipt();
        $SalesReceipt->setDepositToAccountRef($this->_undeposited_funds_account);
        $SalesReceipt->setDocNumber($docNumber);
        $SalesReceipt->setTxnDate(date('Y-m-d'));

        $this->SalesReceipt = $SalesReceipt;

        return $this;
    }

    /**
     * Save
     *
     * @return mixed
     */
    protected function _save()
    {
        $SalesReceiptService = new QuickBooks_IPP_Service_SalesReceipt();

        if ($resp = $SalesReceiptService->add($this->Context, $this->realm, $this->SalesReceipt)) {
            return $resp;
        }
        else throw new Exception($SalesReceiptService->lastError($this->Context));
    }
}