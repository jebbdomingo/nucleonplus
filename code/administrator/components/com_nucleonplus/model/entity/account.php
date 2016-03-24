<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * Account Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
 */
class ComNucleonplusModelEntityAccount extends KModelEntityRow
{
    /**
     * Get direct referral accounts
     *
     * @return array
     */
    public function getDirectReferrals()
    {
        return $this->getObject('com:nucleonplus.model.accounts')->sponsor_id($this->account_number)->fetch();
    }

    /**
     * Get purchases
     *
     * @return array
     */
    public function getPurchases()
    {
        return $this->getObject('com:nucleonplus.model.orders')->account_id($this->id)->fetch();
    }

    /**
     * Save
     *
     * @return string
     */
    public function save()
    {
        // Only one account is allowed for each user
        if ($this->user_id && $this->isNew())
        {
            $account = $this->getObject('com:nucleonplus.model.accounts')->user_id($this->user_id)->fetch();

            // Check if an account if the same user id exists
            if ($account->id)
            {
                $this->setStatusMessage($this->getObject('translator')->translate('An account already exist for this member'));
                $this->setStatus(KDatabase::STATUS_FAILED);

                return;
            }
        }

        parent::save();

        // Generate and set account number
        return $this->_generateAccountNumber();
    }

    /**
     * Generate account number
     *
     * @return KModelEntityRow
     */
    private function _generateAccountNumber()
    {
        $this->account_number = date('ymd') . "-{$this->user_id}-{$this->getProperty('id')}";

        return parent::save();
    }

    /**
     * Get the Account ID from the Account Number
     *
     * @return integer
     */
    public function getIdFromAccountNumber()
    {
        return $this->_extractIdFromNumber($this->account_number);
    }

    /**
     * Get the Account ID from the Sponsor Account Number
     *
     * @return integer
     */
    public function getIdFromSponsor()
    {
        return $this->_extractIdFromNumber($this->sponsor_id);
    }

    /**
     * Extract the Account ID from the Account Number
     *
     * @param string $accountNumber
     *
     * @return integer
     */
    private function _extractIdFromNumber($accountNumber)
    {
        $accountNumber = explode('-', $accountNumber);
        
        return (int) array_pop($accountNumber);
    }

    /**
     * Prevent deletion of account
     * An account can only be deactivated or terminated but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }
}