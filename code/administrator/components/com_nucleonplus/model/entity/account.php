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
    const STATUS_NEW        = 'new';
    const STATUS_PENDING    = 'pending';
    const STATUS_TERMINATED = 'terminated';

    const STATUS_ACTIVE  = 'active';
    const STATUS_DELETED = 'deleted';

    /**
     * Get direct referral accounts
     *
     * @return array
     */
    public function getDirectReferrals()
    {
        return $this->getObject('com://admin/nucleonplus.model.accounts')->sponsor_id($this->id)->fetch();
    }

    /**
     * Get latest purchases
     *
     * @param integer $limit [optional]
     *
     * @return array
     */
    public function getLatestPurchases($limit = 5)
    {
        return $this->getObject('com://admin/nucleonplus.model.orders')
            ->account_id($this->id)
            ->sort('id')
            ->direction('desc')
            ->limit($limit)
            ->fetch()
        ;
    }

    /**
     * Get sponsor's account
     *
     * @return KModelEntityInterface
     */
    public function getSponsor()
    {
        return $this->getObject('com://admin/nucleonplus.model.accounts')->id($this->sponsor_id)->fetch();
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
            $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($this->user_id)->fetch();

            // Check if an account for that user id exists
            if ($account->id)
            {
                $this->setStatusMessage($this->getObject('translator')->translate('An account already exist for this user'));
                $this->setStatus(KDatabase::STATUS_FAILED);
                return false;
            }
            else return $this->_generateAccountNumber();
        }

        return parent::save();
    }

    /**
     * Generate account number
     *
     * @return KModelEntityRow
     */
    private function _generateAccountNumber()
    {
        $this->id = date('ymd') . "-{$this->user_id}";

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

    /**
     * Activate
     * Encapsulate activation business logic
     *
     * @throws Exception
     *
     * @return void
     */
    public function activate()
    {
        if ($this->CustomerRef == 0) {
            throw new Exception("Account Activation Error: CustomerRef is required");
        }

        $this->status = 'active';
    }

    /**
     * Get available direct referral bonus
     *
     * @return float
     */
    public function getDirectReferralBalance()
    {
        $rewards = $this->getObject('com://admin/nucleonplus.model.rewards')
            ->getDirectReferralBonus($this->id);

        $payouts = $this->getObject('com://admin/nucleonplus.model.payouts')
            ->getDirectReferralPayout($this->id);

        return $rewards - $payouts;
    }

    /**
     * Get available indirect referral bonus
     *
     * @return float
     */
    public function getIndirectReferralBalance()
    {
        $rewards = $this->getObject('com://admin/nucleonplus.model.rewards')
            ->getIndirectReferralBonus($this->id);

        $payouts = $this->getObject('com://admin/nucleonplus.model.payouts')
            ->getIndirectReferralPayout($this->id);

        return $rewards - $payouts;
    }

    /**
     * Get available rebates
     *
     * @return float
     */
    public function getRebatesBalance()
    {
        $rewards = $this->getObject('com://admin/nucleonplus.model.rewards')
            ->getRebates($this->id);

        $payouts = $this->getObject('com://admin/nucleonplus.model.payouts')
            ->getRebatesPayout($this->id);

        return $rewards - $payouts;
    }

    /**
     * Get total available rewards
     *
     * @return [type] [description]
     */
    public function getAvailableRewards()
    {
        return $this->getDirectReferralBalance() + $this->getIndirectReferralBalance() + $this->getRebatesBalance();
    }
}