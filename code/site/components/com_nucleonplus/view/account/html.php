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
        $model  = $this->getModel();
        $entity = $model->fetch();

        $context->data->bonus   = $model->getTotalReferralBonus()->total;
        $context->data->rebates = $model->getTotalRebates()->total;
        $context->data->total   = ($context->data->bonus + $context->data->rebates);

        parent::_fetchData($context);
    }
}