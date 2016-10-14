<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityOrder extends KModelEntityRow
{
    const STATUS_PROCESSING   = 'processing';
    const STATUS_PAYMENT      = 'awaiting_payment';
    const STATUS_VERIFICATION = 'awaiting_verification';

    const PAYMENT_METHOD_DRAGONPAY = 'dragonpay';
    
    /**
     * Prevent deletion of order
     * An order can only be void but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Save action
     *
     * @return boolean
     */
    public function save()
    {
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($this->account_id)->fetch();

        switch ($account->status)
        {
            case 'new':
            case 'pending':
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account is currently inactive'));
                return false;
                break;

            case 'terminated':
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account was terminated'));
                return false;
                break;
            
            default:
                return parent::save();
                break;
        }
    }

    public function getPropertyTotal()
    {
        return $this->getTotal();
    }

    // /**
    //  * Get the package items of this order
    //  *
    //  * @return array
    //  */
    // public function getItems()
    // {
    //     return $this->getObject('com:nucleonplus.model.packageitems')->package_id($this->package_id)->fetch();
    // }

    /**
     * Get order items
     *
     * @return array
     */
    public function getOrderItems()
    {
        //var_dump($this->getObject('com://admin/nucleonplus.model.orderitems')->order_id($this->id)->fetch);
        return $this->getObject('com://admin/nucleonplus.model.orderitems')->order_id($this->id)->fetch();
    }

    /**
     * Get the package details
     *
     * @return array
     */
    public function getPackage()
    {
        return $this->getObject('com:nucleonplus.model.packages')->id($this->package_id)->fetch();
    }

    /**
     * Get the reward details
     *
     * @return array
     */
    public function getReward()
    {
        return $this->getObject('com:nucleonplus.model.rewards')->product_id($this->id)->fetch();
    }

    // public function getShippingRate()
    // {
    //     return $this->getObject('com://admin/nucleonplus.model.shippingrates')
    //         ->destination($this->_account_state)
    //         ->fetch()
    //     ;
    // }

    public function getAmount()
    {
        return (float) $this->getObject('com://admin/nucleonplus.model.orders')
            ->id($this->id)
            ->getAmount()
        ;
    }

    public function getTotal()
    {
        return $this->getAmount() + (float) $this->shipping_cost + (float) $this->payment_charge;
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

    // public function getWeight()
    // {
    //     return $this->getObject('com://admin/nucleonplus.model.orders')
    //         ->id($this->id)
    //         ->getWeight()
    //     ;
    // }
}