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
 * Member's Rebate Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
 */
class ComNucleonplusModelEntityReward extends KModelEntityRow
{
    /**
     * Process member's rebates
     *
     * @return boolean|void
     */
    public function processRebate()
    {
        if ($this->status <> 'active') {
            return;
        }

        $slots  = $this->getObject('com:nucleonplus.model.slots')->reward_id($this->id)->fetch();
        $payout = 0;
        $data   = array();
        
        foreach ($slots as $slot)
        {
            if ($slot->lf_slot_id == 0 || $slot->rt_slot_id == 0)
            {
                $payout = 0;
                break;
            }
            else
            {
                $leftSlot  = $this->getObject('com:nucleonplus.model.slots')->id($slot->lf_slot_id)->fetch();
                $rightSlot = $this->getObject('com:nucleonplus.model.slots')->id($slot->rt_slot_id)->fetch();

                $payout += $leftSlot->prpv;
                $payout += $rightSlot->prpv;

                $data[] = array(
                    'reward_id'   => $leftSlot->reward_id,
                    'account_id'  => $slot->customer_id,
                    'reward_type' => 'pr', // Product Rebates
                    'credit'      => $leftSlot->prpv
                );
                $data[] = array(
                    'reward_id'   => $rightSlot->reward_id,
                    'account_id'  => $slot->customer_id,
                    'reward_type' => 'pr', // Product Rebates
                    'credit'      => $rightSlot->prpv
                );
            }
        }

        if ($payout == (($this->prpv * $this->slots) * 2))
        {
            $controller = $this->getObject('com:nucleonplus.controller.transaction');

            foreach ($data as $datum) {
                $controller->add($datum);
            }

            $this->status = 'ready';
            $this->save();
        }
    }
}