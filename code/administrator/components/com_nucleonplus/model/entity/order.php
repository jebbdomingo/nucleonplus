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

    const PAYMENT_METHOD_CASH      = 'cash';
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
            case ComNucleonplusModelEntityAccount::STATUS_NEW:
            case ComNucleonplusModelEntityAccount::STATUS_PENDING:
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account is currently inactive'));
                return false;
                break;

            case ComNucleonplusModelEntityAccount::STATUS_TERMINATED:
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account was terminated'));
                return false;
                break;
            
            default:
                return parent::save();
                break;
        }
    }

    /**
     * Calculate order totals
     *
     * @return KModelEntityInterface
     */
    public function calculate()
    {
        // Calculate total
        $this->sub_total = $this->getAmount();
        $this->total     = $this->sub_total + (float) $this->shipping_cost + (float) $this->payment_charge;

        return $this;
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
