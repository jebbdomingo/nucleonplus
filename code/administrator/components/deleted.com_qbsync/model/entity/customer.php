<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComQbsyncModelEntityCustomer extends ComQbsyncQuickbooksModelEntityRow
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
        if ($this->synced == 'yes') {
            $this->setStatusMessage("Customer #{$this->id} is already synced");
            return false;
        }

        $CustomerService = new QuickBooks_IPP_Service_Customer();

        if ($this->action == 'add')
        {
            $Customer = $this->_buildCustomer(new QuickBooks_IPP_Object_Customer());
            if ($resp = $CustomerService->add($this->Context, $this->realm, $Customer))
            {
                $this->synced      = 'yes';
                $this->CustomerRef = QuickBooks_IPP_IDS::usableIDType($resp);
                $this->save();
                return true;
            }
            else $this->setStatusMessage($CustomerService->lastError($this->Context));
        }
        elseif ($this->action == 'update')
        {
            $customers = $CustomerService->query($this->Context, $this->realm, "SELECT * FROM Customer WHERE Id = '{$this->CustomerRef}' ");
            $Customer  = $this->_buildCustomer($customers[0]);
            if ($CustomerService->update($this->Context, $this->realm, $Customer->getId(), $Customer))
            {
                $this->synced = 'yes';
                $this->save();
                return true;
            }
            else $this->setStatusMessage($CustomerService->lastError($this->Context));
        }

        return false;
    }

    /**
     * Build the Customer object
     *
     * @param QuickBooks_IPP_Object_Customer $Customer
     *
     * @return QuickBooks_IPP_Object_Customer
     */
    protected function _buildCustomer($Customer)
    {
        $Customer->setDisplayName($this->DisplayName);

        // Phone #
        $PrimaryPhone = new QuickBooks_IPP_Object_PrimaryPhone();
        $PrimaryPhone->setFreeFormNumber($this->PrimaryPhone);
        $Customer->setPrimaryPhone($PrimaryPhone);

        // Mobile #
        $Mobile = new QuickBooks_IPP_Object_Mobile();
        $Mobile->setFreeFormNumber($this->Mobile);
        $Customer->setMobile($Mobile);

        // Bill address
        $BillAddr = new QuickBooks_IPP_Object_BillAddr();
        $BillAddr->setLine1($this->Line1);
        $BillAddr->setCity($this->City);
        $BillAddr->setState($this->State);
        $BillAddr->setPostalCode($this->PostalCode);
        $BillAddr->setCountry($this->Country);
        $Customer->setBillAddr($BillAddr);

        // Email
        $PrimaryEmailAddr = new QuickBooks_IPP_Object_PrimaryEmailAddr();
        $PrimaryEmailAddr->setAddress($this->PrimaryEmailAddr);
        $Customer->setPrimaryEmailAddr($PrimaryEmailAddr);

        return $Customer;
    }
}