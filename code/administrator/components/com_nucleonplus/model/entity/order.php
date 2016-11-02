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
    const STATUS_COMPLETED    = 'completed';

    const INVOICE_STATUS_SENT = 'sent';
    const INVOICE_STATUS_PAID = 'paid';

    const SHIPPING_METHOD_XEND = 'xend';

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

    /**
     * Get order items
     *
     * @return array
     */
    public function getOrderItems()
    {
        return $this->getObject('com://admin/nucleonplus.model.orderitems')->order_id($this->id)->fetch();
    }

    /**
     * Get the rewards details
     *
     * @return array
     */
    public function getRewards()
    {
        return $this->getObject('com://admin/nucleonplus.model.rewards')->product_id($this->id)->fetch();
    }

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
}
