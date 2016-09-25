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
        $user  = $this->getObject('user');
        $model = $this->getModel();
        $cart  = $model->account_id($user->getId())->fetch();
        $total = 0;

        $context->data->cart = $cart;

        foreach ($cart as $item) {
            $total += $item->_package_price * $item->quantity;
        }

        $context->data->total = $total;

        parent::_fetchData($context);
    }
}