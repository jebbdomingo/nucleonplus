<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComQbsyncModelEntitySalesreceipt extends ComQbsyncQuickbooksModelEntityRow
{
    /**
     *
     * @return KModelEntityRowset
     */
    public function getLineItems()
    {
        return $this->getObject('com:qbsync.model.salesreceiptlines')->SalesReceipt($this->id)->fetch();
    }

    /**
     * Sync to QBO
     *
     * @throws Exception QBO sync error
     * 
     * @return boolean
     */
    public function sync()
    {
        if ($this->synced == 'yes') {
            $this->setStatusMessage("Sales Receipt #{$this->id} with DocNumber {$this->DocNumber} is already synced");
            return false;
        }

        $SalesReceipt = new QuickBooks_IPP_Object_SalesReceipt();
        $SalesReceipt->setDepositToAccountRef($this->DepositToAccountRef);
        $SalesReceipt->setDocNumber($this->DocNumber);
        $SalesReceipt->setTxnDate($this->TxnDate);

        if ($this->CustomerRef > 0) {
            $SalesReceipt->setCustomerRef($this->CustomerRef);
        }

        foreach ($this->getLineItems() as $line)
        {
            $Line = new QuickBooks_IPP_Object_Line();
            $Line->setDetailType('SalesItemLineDetail');
            $Line->setDescription($line->Description);
            $Line->setAmount($line->Amount);

            $Details = new QuickBooks_IPP_Object_SalesItemLineDetail();
            $Details->setItemRef($line->ItemRef);
            $Details->setQty($line->Qty);

            $Line->addSalesItemLineDetail($Details);

            $SalesReceipt->addLine($Line);
        }

        $SalesReceiptService = new QuickBooks_IPP_Service_SalesReceipt();

        if ($resp = $SalesReceiptService->add($this->Context, $this->realm, $SalesReceipt))
        {
            $this->synced = 'yes';
            $this->save();

            $this->_syncTransfers();

            return true;
        }
        else $this->setStatusMessage($SalesReceiptService->lastError($this->Context));

        return false;
    }

    /**
     * Sync releated account funds transfers
     *
     * @return boolean
     */
    protected function _syncTransfers()
    {
        $transfers = $this->getObject('com:qbsync.model.transfers')->order_id($this->DocNumber)->fetch();

        foreach ($this->getObject('com:qbsync.model.transfers')->order_id($this->DocNumber)->fetch() as $transfer)
        {
            if (!$transfer->sync())
            {
                $this->setStatusMessage("Syncing Related Transfer Transaction #{$transfer->id} failed for Sales Receipt with Doc Number {$this->DocNumber}");
                return false;
            }
        }
    }

    /**
     * Delete
     *
     * @return boolean
     */
    public function delete()
    {
        foreach ($this->getLineItems() as $line)
        {
            if (!$line->delete())
            {
                $this->setStatusMessage("Deleting Sales Receipt Item #{$line->id} failed");
                return false;
            }
        }

        $transfers = $this->getObject('com:qbsync.model.transfers')->order_id($this->DocNumber)->fetch();

        foreach ($this->getObject('com:qbsync.model.transfers')->order_id($this->DocNumber)->fetch() as $transfer)
        {
            if (!$transfer->delete())
            {
                $this->setStatusMessage("Deleting Related Transfer Transaction #{$transfer->id} failed for Sales Receipt with Doc Number {$this->DocNumber}");
                return false;
            }
        }

        return parent::delete();
    }
}