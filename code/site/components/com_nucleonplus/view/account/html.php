<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewAccountHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $model   = $this->getModel();
        $account = $model->fetch();

        // Rewards summary
        $context->data->total_referral_bonus = $model->getTotalAvailableReferralBonus()->total;
        $context->data->total_rebates        = $model->getTotalAvailableRebates()->total;
        $context->data->total_bonus          = ($context->data->total_referral_bonus + $context->data->total_rebates);

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

        $context->data->rebates = $this->getObject('com://admin/nucleonplus.model.rebates')
            ->customer_id($account->id)
            ->payout_id(0)
            ->fetch()
        ;

        parent::_fetchData($context);
    }
}