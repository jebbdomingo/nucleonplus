<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusViewHtml extends ComKoowaViewHtml
{
    protected function _fetchData(KViewContext $context)
    {
        $data = $this->getObject('com://admin/nucleonplus.accounting.service.data');

        $context->data->onlinePurchaseEnabled = $data->CONFIG_ONLINE_PURCHASE_ENABLED;
        $context->data->isAuthenticated       = $this->getObject('user')->isAuthentic();

        parent::_fetchData($context);
    }
}