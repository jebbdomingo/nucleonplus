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

class ComNucleonplusAccountingServiceJournal extends ComNucleonplusAccountingServiceObject implements ComNucleonplusAccountingServiceJournalInterface
{
    /**
     * Item Service
     *
     * @var ComNucleonplusAccountingServiceItemInterface
     */
    protected $_item_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Item service
        $identifier   = $this->getIdentifier($config->item_service);
        $item_service = $this->getObject($identifier);

        if (!($item_service instanceof ComNucleonplusAccountingServiceItemInterface))
        {
            throw new UnexpectedValueException(
                "Item Service $identifier does not implement ComNucleonplusAccountingServiceItemInterface"
            );
        }
        else $this->_item_service = $item_service;
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
            'item_service' => 'com:nucleonplus.accounting.service.item',
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
     * Record sale in accounting system
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    public function recordCostOfGoodsSold(KModelEntityInterface $order)
    {
        $this->_createJournalEntry($order->id);

        foreach ($order->getItems() as $item)
        {
            $inventoryItem = $this->_item_service->find($item->inventory_item_id);

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
        }

        $this->_save();
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