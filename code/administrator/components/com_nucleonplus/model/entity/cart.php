<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityCart extends ComCartModelEntityCart implements ComNucleonplusModelEntityCartInterface
{
    const INTERFACT_SITE  = 'site';
    const INTERFACT_ADMIN = 'admin';

    public function save()
    {
        $result = false;

        if (!$this->isNew() && $this->interface == self::INTERFACT_SITE)
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
        return (float) $this->getObject('com://admin/nucleonplus.model.carts')
            ->customer($this->customer)
            ->getAmount()
        ;
    }

    public function getShippingFee()
    {
        $city = $this->getObject('com://admin/nucleonplus.model.cities')->id($this->city_id)->fetch();
        $dest = $city->_province_id == ComNucleonplusModelEntityCity::DESTINATION_METRO_MANILA ? 'manila' : 'provincial';

        return $this->getShippingCost($dest, $this->getWeight());
    }

    public function getSubTotal()
    {
        return $this->getAmount() + $this->getShippingFee();
    }

    public function getWeight()
    {
        return $this->getObject('com://admin/nucleonplus.model.carts')
            ->cart_id($this->id)
            ->getWeight()
        ;
    }
}
