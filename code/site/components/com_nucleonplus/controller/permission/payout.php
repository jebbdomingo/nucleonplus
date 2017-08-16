<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerPermissionPayout extends ComKoowaControllerPermissionAbstract
{
    /**
     * Specialized permission check
     * Checks if the user can add an order
     *
     * @return boolean
     */
    public function canAdd()
    {
        $data         = $this->getContext()->request->data;
        $claimRequest = $this->getObject('com:nucleonplus.model.configs')->item(ComNucleonplusModelEntityConfig::PAYOUT_CLAIM_REQUEST_NAME)->fetch();
        $result       = false;

        if ($claimRequest->value == ComNucleonplusModelEntityConfig::CLAIM_REQUEST_ENABLED)
        {
            // Ensure member has no outstanding payout request
            if (!$this->hasOutstandingRequest())
            {
                // Ensure bank details is complete
                if ($this->checkBankDetails())
                {
                    // Ensure minimum amount of payout
                    $result = $this->checkMinimumAmount();
                }
            }
        }

        return $result;
    }

    public function hasOutstandingRequest()
    {
        $account = $this->getObject('com:nucleonplus.useraccount')
            ->getAccount()
            ->id;

        return $this->getModel()->account($account)->hasOutstandingRequest();
    }

    public function checkBankDetails()
    {
        $user_account    = $this->getObject('com:nucleonplus.useraccount');
        $account = $user_account->getAccount();

        $bank       = trim($account->bank_name);
        $acctNumber = trim($account->bank_account_number);
        $acctName   = trim($account->bank_account_name);
        $mobile     = trim($account->mobile);
        $result     = false;

        if (!empty($bank) && !empty($acctNumber) && !empty($acctName) && !empty($mobile)) {
            $result = true;
        }

        return $result;
    }

    public function checkMinimumAmount()
    {
        $result       = false;
        $user_account = $this->getObject('com:nucleonplus.useraccount');
        $account      = $user_account->getAccount();
        $minAmount    = (float) $this->getObject('com:nucleonplus.model.configs')
            ->item(ComNucleonplusModelEntityConfig::PAYOUT_MIN_AMOUNT_NAME)
            ->fetch()
            ->value;

        if ($account->getAvailableRewards() >= $minAmount) {
            $result = true;
        }

        return $result;
    }
}
