<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * Order Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
 */
class ComNucleonplusModelEntityOrder extends KModelEntityRow
{
    /**
     * Process reward, check if this order is ready for payout
     *
     * @return array
     */
    public function processReward()
    {
        if ($this->payout) {
            return false;
        }

        $slots  = $this->getObject('com:nucleonplus.model.slots')->product_id($this->id)->fetch();
        $payout = 0;
        
        foreach ($slots as $slot)
        {
            if (is_null($slot->lf_slot_id) || is_null($slot->rt_slot_id)) {
                $payout = null;

                break;
            } else {
                $payout += 1100;
            }
        }

        // var_dump($this->id);
        // var_dump($payout);

        $this->payout = $payout;
        $this->save();
    }

    /**
     * Get Account ID from the Account Number
     *
     * @return string
     */
    public function getAccountId()
    {
        $accountNumber = explode('-', $this->account_number);

        return (int) array_pop($accountNumber);
    }
}