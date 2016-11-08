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
        $user    = $this->getObject('user');
        $model   = $this->getObject('com://admin/nucleonplus.model.accounts')->id($user->getId());
        $account = $model->fetch();

        // Rewards summary
        $context->data->total_referral_bonus   = $model->getTotalAvailableReferralBonus()->total;
        $context->data->total_patronages       = $model->getTotalAvailablePatronages()->total;
        $context->data->total_direct_referrals = $model->getTotalAvailableDirectReferrals()->total;
        $context->data->total_rebates          = $model->getTotalAvailableRebates()->total;

        $context->data->total_bonus = (
            $context->data->total_referral_bonus +
            $context->data->total_patronages +
            $context->data->total_direct_referrals +
            $context->data->total_rebates
        );

        // Rewards payout details
        $context->data->dr_bonuses = $this->getObject('com://admin/nucleonplus.model.referralbonuses')
            ->account_id($account->id)
            ->referral_type('dr')
            ->payout_id(0)
            ->fetch()
        ;

        $context->data->ir_bonuses = $this->getObject('com://admin/nucleonplus.model.referralbonuses')
            ->account_id($account->id)
            ->referral_type('ir')
            ->payout_id(0)
            ->fetch()
        ;

        $context->data->patronages = $this->getObject('com://admin/nucleonplus.model.patronagebonuses')
            ->customer_id($account->id)
            ->payout_id(0)
            ->fetch()
        ;

        $context->data->direct_referrals = $this->getObject('com://admin/nucleonplus.model.directreferrals')
            ->account_id($account->id)
            ->payout_id(0)
            ->fetch()
        ;

        $context->data->rebates = $this->getObject('com://admin/nucleonplus.model.rebates')
            ->account_id($account->id)
            ->payout_id(0)
            ->fetch()
        ;

        parent::_fetchData($context);
    }
}