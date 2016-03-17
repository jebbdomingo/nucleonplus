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
        $this->_undeposited_funds_account = $config->undeposited_funds_account;
        $this->_sales_of_product_account = $config->sales_of_product_account;
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
            'inventory_service'         => 'com:nucleonplus.accounting.service.inventory',
            'undeposited_funds_account' => 92,
            'sales_of_product_account'  => 124,
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
     * Record sale and cost of goods sold in the accounting system 
     *
     * @param KModelEntityInterface $order [description]
     *
     * @return [type]                [description]
     */
    public function recordSale(KModelEntityInterface $order)
    {
        $this->_createJournalEntry($order->id);

        foreach ($order->getItems() as $item)
        {
            $inventoryItem = $this->_inventory_service->find($item->inventory_item_id);

            // Cost of goods sold transaction
            $this->_createDebitLine(array(
                'account'     => QuickBooks_IPP_IDS::usableIDType($inventoryItem->getExpenseAccountRef()),
                'description' => 'test',
                'amount'      => ($inventoryItem->getPurchaseCost() * $item->quantity),
            ));

            $this->_createCreditLine(array(
                'account'     => QuickBooks_IPP_IDS::usableIDType($inventoryItem->getAssetAccountRef()),
                'description' => 'test',
                'amount'      => ($inventoryItem->getPurchaseCost() * $item->quantity),
            ));

            // Sale transaction
            $this->_createDebitLine(array(
                'account'     => $this->_undeposited_funds_account,
                'description' => 'test',
                'amount'      => ($inventoryItem->getUnitPrice() * $item->quantity),
            ));

            $this->_createCreditLine(array(
                'account'     => $this->_sales_of_product_account,
                'description' => 'test',
                'amount'      => ($inventoryItem->getUnitPrice() * $item->quantity),
            ));
        }

        $this->_save();
    }

    public function recordSalesAllocations(KModelEntityInterface $order) {}

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