<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewCartHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $query   = $this->getObject('request')->query;
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($query->customer)->fetch();

        $context->data->account = $account;
    }
}