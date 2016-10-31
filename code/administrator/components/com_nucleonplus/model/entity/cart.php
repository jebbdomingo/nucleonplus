<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityCart extends ComCartModelEntityCart
{
    public function save()
    {
        $result = false;

        if (!$this->isNew() && $this->interface == 'site')
        {
            if (empty($this->address) || empty($this->city))
            {
                $this->setStatus(KDatabase::STATUS_FAILED);
                $this->setStatusMessage('Shipping address is required');
            }
            else $result = parent::save();
        }
        else $result = parent::save();

        return $result;
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
            ->customer($this->customer)
            ->getAmount()
        ;
    }

    public function getShippingCost()
    {
        $city = $this->getObject('com://admin/nucleonplus.model.cities')->id($this->city_id)->fetch();

        return $this->getObject('com://admin/nucleonplus.model.shippingrates')
            ->getRate($city->_province_id, $this->getWeight())
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
        $amount = 0;

        if ($this->payment_mode)
        {
            $rate = $this->getObject('com://admin/nucleonplus.model.paymentrates')
                ->mode($this->payment_mode)
                ->fetch()
            ;
            
            $amount = $rate->amount;
        }

        return (float) $amount;
    }

    public function getPaymentMode()
    {
        $description = null;

        if ($this->payment_mode)
        {
            $entity =  $this->getObject('com://admin/nucleonplus.model.paymentrates')
                ->mode($this->payment_mode)
                ->fetch()
            ;

            $description = $entity->description;
        }


        return $description;
    }
}
