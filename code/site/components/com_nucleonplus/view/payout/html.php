<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewPayoutHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $user_account  = $this->getObject('com:nucleonplus.useraccount');
        $account = $user_account->getAccount();

        // Rewards summary
        $context->data->direct_referrals   = $account->getDirectReferralBalance();
        $context->data->indirect_referrals = $account->getIndirectReferralBalance();
        $context->data->rebates            = $account->getRebatesBalance();

        $context->data->total = (
            $context->data->rebates +
            $context->data->direct_referrals +
            $context->data->indirect_referrals
        );

        parent::_fetchData($context);
    }
}