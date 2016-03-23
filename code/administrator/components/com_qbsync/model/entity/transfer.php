<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComQbsyncModelEntityTransfer extends ComQbsyncQuickbooksModelEntityRow
{
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
            $this->setStatusMessage("Transfer #{$this->id} is already synced");
            return false;
        }

        $Transfer = new QuickBooks_IPP_Object_Transfer();
        $Transfer->setFromAccountRef($this->FromAccountRef);
        $Transfer->setToAccountRef($this->ToAccountRef);
        $Transfer->setAmount($this->Amount);
        $Transfer->setPrivateNote($this->PrivateNote);

        $TransferService = new QuickBooks_IPP_Service_Transfer();

        if ($resp = $TransferService->add($this->Context, $this->realm, $Transfer))
        {
            $this->synced = 1;
            $this->save();

            return true;
        }
        else $this->setStatusMessage($TransferService->lastError($this->Context));

        return false;
    }
}