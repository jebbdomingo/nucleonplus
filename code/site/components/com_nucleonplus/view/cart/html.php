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
        $user          = $this->getObject('user');
        $account       = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user->getId())->fetch();
        $cart          = $this->getModel()->account_id($user->getId())->fetch();
        $amount        = $cart->getAmount();
        $shippingCost  = $cart->getShippingCost();
        $paymentCharge = $cart->getPaymentCharge();
        $total         = ($amount + $shippingCost + $paymentCharge);

        $context->data->cart           = $cart;
        $context->data->address        = $cart->address ? $cart->address : $account->street;
        $context->data->city           = $cart->city ? $cart->city : $account->city;
        $context->data->state_province = $cart->state_province ? $cart->state_province : $account->state;
        $context->data->region         = $cart->region;
        $context->data->items          = $cart->getItems() ? $cart->getItems() : array();

        $context->data->amount        = number_format($amount, 2);
        $context->data->shipping_cost = null;

        if ($cart->region)
        {
            $context->data->shipping_cost = number_format($shippingCost, 2);
            $context->data->payment_fee   = number_format($paymentCharge, 2);
        }

        $context->data->total = number_format($total, 2);

        parent::_fetchData($context);
    }
}