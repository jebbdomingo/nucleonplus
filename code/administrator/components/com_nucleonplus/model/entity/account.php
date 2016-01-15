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
        return $this->getObject('com:nucleonplus.model.orders')->account_number($this->account_number)->fetch();
    }

    public function save()
    {
        // Only one account is allowed for each user
        if ($this->user_id && $this->isNew()) {
            $account = $this->getObject('com:nucleonplus.model.accounts')->user_id($this->user_id)->fetch();

            // Check if an account if the same user id exists
            if ($account->id) {
                $this->setStatusMessage($this->getObject('translator')->translate('An account already exist for this member'));
                $this->setStatus(KDatabase::STATUS_FAILED);

                return;
            }
        }

        parent::save();

        // Generate and set account number
        return $this->generateAccountNumber();
    }

    private function generateAccountNumber()
    {
        $this->account_number = date('ymd') . "-{$this->user_id}-{$this->getProperty('id')}";

        return parent::save();
    }
}