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
        $cart          = $this->getModel()->account_id($user->getId())->fetch();
        $amount        = $cart->getAmount();
        $shippingCost  = $cart->getShippingCost();
        $paymentCharge = 20;
        $total         = ($amount + $shippingCost + $paymentCharge);

        $context->data->cart           = $cart;
        $context->data->address        = $cart->address;
        $context->data->city           = $cart->city;
        $context->data->state_province = $cart->state_province;
        $context->data->region         = $cart->region;
        $context->data->items          = $cart->getItems() ? $cart->getItems() : array();

        $context->data->amount        = number_format($amount, 2);
        $context->data->show_charges  = false;
        $context->data->shipping_cost = null;

        if ($cart->region)
        {
            $context->data->show_charges  = true;
            $context->data->shipping_cost = number_format($shippingCost, 2);
            $context->data->payment_fee   = number_format($paymentCharge, 2);
        }

        $context->data->total = number_format($total, 2);

        parent::_fetchData($context);
    }
}