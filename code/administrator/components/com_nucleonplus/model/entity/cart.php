<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityCart extends KModelEntityRow
{
    public function delete()
    {
        $cartItems = $this->getObject('com://admin/nucleonplus.model.cartitems')->cart_id($this->id)->fetch();
        $cartItems->delete();

        parent::delete();
    }

    public function getItems()
    {
        return $this->getObject('com://admin/nucleonplus.model.cartitems')
            ->cart_id($this->id)
            ->fetch()
        ;
    }

    public function getPropertySubtotal()
    {
        return $this->getSubTotal();
    }

    public function getAmount()
    {
        return $this->getObject('com://admin/nucleonplus.model.carts')
            ->account_id($this->account_id)
            ->getAmount()
        ;
    }

    public function getShippingCost()
    {
        return $this->getObject('com://admin/nucleonplus.model.shippingrates')
            ->getRate($this->region, $this->getWeight())
        ;
    }

    public function getSubTotal()
    {
        return (float) $this->getAmount() + (float) $this->getShippingCost();
    }

    public function getWeight()
    {
        return $this->getObject('com://admin/nucleonplus.model.carts')
            ->cart_id($this->id)
            ->getWeight()
        ;
    }

    public function getPaymentCharge()
    {
        $entity =  $this->getObject('com://admin/nucleonplus.model.paymentchannels')
            ->mode($this->payment_channel)
            ->fetch()
        ;

        return $entity->amount;
    }
}