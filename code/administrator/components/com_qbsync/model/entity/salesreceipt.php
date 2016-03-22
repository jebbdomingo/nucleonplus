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
        if ($this->synced == 1) {
            $this->setStatusMessage("Sales Receipt #{$this->id} with DocNumber {$this->DocNumber} is already synced");
            return false;
        }

        $SalesReceipt = new QuickBooks_IPP_Object_SalesReceipt();
        $SalesReceipt->setDepositToAccountRef($this->DepositToAccountRef);
        $SalesReceipt->setDocNumber($this->DocNumber);
        $SalesReceipt->setTxnDate($this->TxnDate);

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
            $this->synced = 1;
            $this->save();

            return true;
        }
        else $this->setStatusMessage($SalesReceiptService->lastError($this->Context));

        return false;
    }
}