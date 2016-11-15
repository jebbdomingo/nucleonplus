<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewOrderHtml extends KViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $order   = $context->data->order;
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($order->account_id)->fetch();

        $context->data->account = $account;
    }
}
