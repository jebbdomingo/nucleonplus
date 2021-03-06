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
    const INTERFACE_SITE  = 'site';
    const INTERFACE_ADMIN = 'admin';

    public function save()
    {
        $result = false;

        if (!$this->isNew() && $this->interface == self::INTERFACE_SITE)
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

    /**
     * Get cart items and its quantities
     *
     * @return array
     */
    public function getItemQuantities()
    {
        $data  = array();
        $items = $this->getObject('com://admin/nucleonplus.model.cartitems')
            ->cart_id($this->id)
            ->fetch()
        ;

        foreach ($items as $item)
        {
            if ($item->_item_type == ComQbsyncModelEntityItem::TYPE_GROUP)
            {
                // Query grouped items
                $groupedItems = $this->getObject('com://admin/qbsync.model.itemgroups')->parent_id($item->_item_ref)->fetch();

                foreach ($groupedItems as $groupItem)
                {
                    if ($groupItem->_item_type == ComQbsyncModelEntityItem::TYPE_INVENTORY_ITEM)
                    {
                        @$data[$groupItem->_item_ref] += (int) $item->quantity * (int) $groupItem->quantity;
                    }
                }
            }
            else @$data[$item->_item_ref] += (int) $item->quantity;
        }

        return $data;
    }

    public function getPropertySubtotal()
    {
        return $this->getSubTotal();
    }

    public function getAmount()
    {
        $app       = JFactory::getApplication();
        $interface = null;

        if ($app->isAdmin()) {
            $interface = self::INTERFACE_ADMIN;
        } else {
            $interface = self::INTERFACE_SITE;
        }

        return (float) $this->getObject('com://admin/nucleonplus.model.carts')
            ->interface($interface)
            ->customer($this->customer)
            ->getAmount()
        ;
    }

    /**
     * Get total shipping fee
     *
     * @return float
     */
    public function getShippingFee()
    {
        $amount   = 0;
        $couriers = $this->getShippingFees();

        if (count($couriers) > 1)
        {
            // Multiple couriers
            foreach ($couriers as $courier) {
                $amount += @$courier['amount'];
            }
        }
        else $amount = @$couriers[0]['amount'];

        return $amount;
    }

    /**
     * Get breakdown shipping fees from multiple courier
     *
     * @return array of couriers
     */
    public function getShippingFees()
    {
        $city   = $this->getObject('com://admin/nucleonplus.model.cities')->id($this->city_id)->fetch();
        $dest   = $city->_province_id == ComNucleonplusModelEntityCity::DESTINATION_METRO_MANILA ? 'manila' : 'provincial';

        // Compute shipping cost for each of the items
        $items = $this->getObject('com://admin/nucleonplus.model.cartitems')
            ->cart_id($this->id)
            ->fetch()
        ;

        $couriers = array();
        foreach ($items as $item) {
            @$couriers[$item->_item_shipping_type] = null;
        }

        $hasPost = array_key_exists(ComQbsyncModelEntityItem::TYPE_SHIPPING_POST, $couriers);
        $hasXend = array_key_exists(ComQbsyncModelEntityItem::TYPE_SHIPPING_XEND, $couriers);

        $couriers = array();

        // If the cart has items that both shipped with post and xend, we use xend for all
        if ($hasPost && $hasXend)
        {
            $weight     = $this->getWeight();
            $couriers[] = array(
                'name'   => ComQbsyncModelEntityItem::TYPE_SHIPPING_XEND,
                'weight' => $weight,
                'amount' => $this
                    ->getObject('com:xend.model.shippingrates')
                    ->getRate($dest, $weight)
            );
        }
        else
        {
            $postWeight = 0;
            $xendWeight = 0;

            foreach ($items as $item)
            {
                switch ($item->_item_shipping_type)
                {
                    case ComQbsyncModelEntityItem::TYPE_SHIPPING_POST:
                        // Philippine post office
                        $postWeight += $this->getWeight(ComQbsyncModelEntityItem::TYPE_SHIPPING_POST);
                        break;
                    
                    default:
                        $xendWeight += $this->getWeight(ComQbsyncModelEntityItem::TYPE_SHIPPING_XEND);
                        break;
                }
            }

            if ($postWeight)
            {
                $couriers[] = array(
                    'name'   => ComQbsyncModelEntityItem::TYPE_SHIPPING_POST,
                    'weight' => $postWeight,
                    'amount' => $this
                        ->getObject('com:phlpost.service.shippingrates')
                        ->getRate($dest, $postWeight)
                );
            }

            if ($xendWeight) {
                $couriers[] = array(
                    'name'   => ComQbsyncModelEntityItem::TYPE_SHIPPING_XEND,
                    'weight' => $xendWeight,
                    'amount' => $this
                        ->getObject('com:xend.model.shippingrates')
                        ->getRate($dest, $xendWeight)
                );
            }
        }

        return $couriers;
    }

    public function getSubTotal()
    {
        return $this->getAmount() + $this->getShippingFee();
    }

    public function getWeight($itemType = null)
    {
        return $this->getObject('com://admin/nucleonplus.model.carts')
            ->cart_id($this->id)
            ->getWeight($itemType)
        ;
    }
}
