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

        parent::_fetchData($context);
    }
}