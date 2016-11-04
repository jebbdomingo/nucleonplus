<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewPayoutsHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $model = $this->getModel();

        // Payouts current total
        $context->data->total = $model->getTotal();

        parent::_fetchData($context);
    }
}