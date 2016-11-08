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
        $user         = $this->getObject('user');
        $account      = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $data         = $this->getContext()->request->data;
        $claimRequest = $this->getObject('com:nucleonplus.model.configs')->item('claim_request')->fetch();
        $result       = false;

        if ($claimRequest->value == ComNucleonplusModelEntityConfig::CLAIM_REQUEST_ENABLED)
        {
            // Ensure member has no outstanding payout request
            if (!$this->getModel()->hasOutstandingRequest($account->id))
            {
                $acctNumber = trim($account->bank_account_number);
                $acctName   = trim($account->bank_account_name);
                $mobile     = trim($account->mobile);

                if (!empty($acctNumber) && !empty($acctName) && !empty($mobile)) {
                    $result = true;
                }
            }
        }

        return $result;
    }
}
