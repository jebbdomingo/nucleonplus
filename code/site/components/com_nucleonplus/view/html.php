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
        $user = $this->getObject('user');
        $data = $this->getObject('com://admin/nucleonplus.accounting.service.data');

        $context->data->onlinePurchaseEnabled = $data->CONFIG_ONLINE_PURCHASE_ENABLED;
        $context->data->isAuthenticated       = $user->isAuthentic();

        // Manager is not allowed to buy from frontend
        if (in_array(6, $user->getGroups())) {
            $canBuy = false;
        }
        else $canBuy = true;

        $context->data->canBuy = $canBuy;

        parent::_fetchData($context);
    }
}