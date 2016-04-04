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
class ComNucleonplusAccountingServiceJournal extends ComNucleonplusAccountingServiceObject implements ComNucleonplusAccountingServiceJournalInterface
{
    /**
     * Item Service
     *
     * @var ComNucleonplusAccountingServiceInventoryInterface
     */
    protected $_inventory_service;

    /**
     * Undeposited funds account
     *
     * @var integer
     */
    protected $_undeposited_funds_account;

    /**
     * Sales of product account
     *
     * @var integer
     */
    protected $_sales_of_product_account;

    /**
     * System fee account
     *
     * @var integer
     */
    protected $_system_fee_account;

    /**
     * Contingency fund account
     *
     * @var integer
     */
    protected $_contingency_fund_account;

    /**
     * Operating expense budget account
     *
     * @var integer
     */
    protected $_operating_expense_budget_account;

    /**
     * Sales account
     *
     * @var integer
     */
    protected $_sales_account;

    /**
     * System fee 
     *
     * @var float
     */
    protected $_system_fee_rate;

    /**
     * Contingecy fund rate
     *
     * @var float
     */
    protected $_contingency_fund_rate;

    /**
     * Operating expense rate
     *
     * @var float
     */
    protected $_operating_expense_rate;

    /**
     * Rebates account
     *
     * @var integer
     */
    protected $_rebates_account;

    /**
     * Referral bonus account
     *
     * @var integer
     */
    protected $_referral_bonus_account;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Item service
        $identifier   = $this->getIdentifier($config->inventory_service);
        $inventory_service = $this->getObject($identifier);

        if (!($inventory_service instanceof ComNucleonplusAccountingServiceInventoryInterface))
        {
            throw new UnexpectedValueException(
                "Item Service $identifier does not implement ComNucleonplusAccountingServiceInventoryInterface"
            );
        }
        else $this->_inventory_service = $inventory_service;

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
            'undeposited_funds_account'        => 92,
            'sales_of_product_account'         => 124,
            'system_fee_account'               => 138,
            'contingency_fund_account'         => 139;
            'operating_expense_budget_account' => 140;
            'sales_account'                    => 96;
            'system_fee_rate'                  => 10.00;
            'contingency_fund_rate'            => 50.00;
            'operating_expense_rate'           => 60.00;
            'rebates_account'                  => 141;
            'referral_bonus_account'           => 142;
        ));

        parent::_initialize($config);
    }

    /**
     * Main journal entry object
     *
     * @var QuickBooks_IPP_Object_JournalEntry
     */
    protected $JournalEntry;

    /**
     * Record sales transaction in the accounting system 
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    public function recordSale(KModelEntityInterface $order)
    {
        $this->_createJournalEntry($order->id);

        foreach ($order->getItems() as $item)
        {
            $inventoryItem = $this->_inventory_service->find($item->inventory_item_id);

            // Cost of goods sold distribution line
            $this->_createCostOfSaleLine($inventoryItem->getPurchaseCost(), $item->quantity);

            // Sales distribution line
            $this->_createSaleLine($inventoryItem->getUnitPrice(), $item->quantity);
        }

        $this->_createSalesAllocationLine($order);

        $this->_save();
    }

    /**
     * Record rebates allocation
     *
     * @param KModelEntityInterface $slot
     *
     * @return void
     */
    public function recordRebatesAllocation(KModelEntityInterface $slot)
    {
        $this->_createJournalEntry($slot->product_id);

        $this->_createDebitLine(array(
            'account'     => $this->_rebates_account,
            'description' => 'Rebates for Members',
            'amount'      => $slot->prpv,
        ));

        $this->_createCreditLine(array(
            'account'     => $this->_sales_account,
            'description' => 'Rebates for Members',
            'amount'      => $slot->prpv,
        ));

        $this->_save();
    }

    /**
     * Record referral bonus allocation
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    public function recordReferralBonusAllocation(KModelEntityInterface $order)
    {
        $this->_createJournalEntry($order->id);

        $this->_createDebitLine(array(
            'account'     => $this->_rebates_account,
            'description' => 'Rebates for Members',
            'amount'      => ($order->_reward_drpv * $order->_reward_slots),
        ));

        $this->_createCreditLine(array(
            'account'     => $this->_sales_account,
            'description' => 'Rebates for Members',
            'amount'      => ($order->_reward_drpv * $order->_reward_slots),
        ));

        $this->_save();
    }

    /**
     * Create cost of goods sold distribution line
     *
     * @param float   $cost
     * @param integer $qty
     *
     * @return void
     */
    protected function _createCostOfSaleLine($cost, $qty)
    {
        $this->_createDebitLine(array(
            'account'     => QuickBooks_IPP_IDS::usableIDType($inventoryItem->getExpenseAccountRef()),
            'description' => 'test',
            'amount'      => ($cost * $qty),
        ));

        $this->_createCreditLine(array(
            'account'     => QuickBooks_IPP_IDS::usableIDType($inventoryItem->getAssetAccountRef()),
            'description' => 'test',
            'amount'      => ($cost * $qty),
        ));
    }

    /**
     * Create sales distribution line
     *
     * @param float   $unitPrice
     * @param integer $qty
     *
     * @return void
     */
    protected function _createSaleLine($unitPrice, $qty)
    {
        $this->_createDebitLine(array(
            'account'     => $this->_undeposited_funds_account,
            'description' => 'test',
            'amount'      => ($unitPrice * $qty),
        ));

        $this->_createCreditLine(array(
            'account'     => $this->_sales_of_product_account,
            'description' => 'test',
            'amount'      => ($unitPrice * $qty),
        ));
    }

    /**
     * Create sales allocations distribution line
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    protected function _createSalesAllocationLine(KModelEntityInterface $order)
    {
        $systemFee       = ($this->_system_fee_rate * $order->_reward_slots);
        $contingencyFund = ($this->_contingency_fund_rate * $order->_reward_slots);
        $operatingFund   = ($this->_operating_expense_rate * $order->_reward_slots);
        $total           = ($systemFee + $contingencyFund + $operatingFund);

        $this->_createDebitLine(array(
            'account'     => $this->_system_fee_account,
            'description' => 'System Fee',
            'amount'      => $systemFee,
        ));

        $this->_createDebitLine(array(
            'account'     => $this->_contingency_fund_account,
            'description' => 'Contingency Fund',
            'amount'      => $contingencyFund,
        ));

        $this->_createDebitLine(array(
            'account'     => $this->_operating_expense_budget_account,
            'description' => 'Operating Expense Fund',
            'amount'      => $operatingFund,
        ));

        $this->_createCreditLine(array(
            'account'     => $this->_sales_account,
            'description' => 'Sales',
            'amount'      => $total,
        ));
    }

    /**
     * Save
     *
     * @return mixed
     */
    protected function _save()
    {
        $JournalEntryService = new QuickBooks_IPP_Service_JournalEntry();

        if ($resp = $JournalEntryService->add($this->Context, $this->realm, $this->JournalEntry)) {
            return $resp;
        }
        else throw new Exception($JournalEntryService->lastError($this->Context));
    }

    /**
     * Create main journal entry object
     *
     * @param string $docNumber
     *
     * @return this
     */
    protected function _createJournalEntry($docNumber)
    {
        $JournalEntry = new QuickBooks_IPP_Object_JournalEntry();
        $JournalEntry->setDocNumber($docNumber);
        $JournalEntry->setTxnDate(date('Y-m-d'));

        $this->JournalEntry = $JournalEntry;

        return $this;
    }

    /**
     * Create debit line
     *
     * @param array $data
     *
     * @return this
     */
    protected function _createDebitLine(array $data)
    {
        $Line = new QuickBooks_IPP_Object_Line();
        $Line->setDescription($data['description']);
        $Line->setAmount($data['amount']);
        $Line->setDetailType('JournalEntryLineDetail');

        $Details = new QuickBooks_IPP_Object_JournalEntryLineDetail();
        $Details->setPostingType('Debit');
        $Details->setAccountRef($data['account']);

        $Line->addJournalEntryLineDetail($Details);

        $this->JournalEntry->addLine($Line);

        return $this;
    }

    /**
     * Create credit line
     *
     * @param array $data
     *
     * @return this
     */
    protected function _createCreditLine(array $data)
    {
        $Line = new QuickBooks_IPP_Object_Line();
        $Line->setDescription($data['description']);
        $Line->setAmount($data['amount']);
        $Line->setDetailType('JournalEntryLineDetail');

        $Details = new QuickBooks_IPP_Object_JournalEntryLineDetail();
        $Details->setPostingType('Credit');
        $Details->setAccountRef($data['account']);

        $Line->addJournalEntryLineDetail($Details);

        $this->JournalEntry->addLine($Line);

        return $this;
    }
}