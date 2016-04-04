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

        $slots         = $this->getObject('com:nucleonplus.model.slots')->reward_id($this->id)->fetch();
        $requiredSlots = ($this->slots * 2);
        $payoutSlots   = 0;
        $payout        = 0;
        $data          = array();
        
        foreach ($slots as $slot)
        {
            if ($slot->lf_slot_id == 0 || $slot->rt_slot_id == 0)
            {
                $payout = 0;
                break;
            }
            else
            {
                $payoutSlots += 2;

                $leftSlot  = $this->getObject('com:nucleonplus.model.slots')->id($slot->lf_slot_id)->fetch();
                $rightSlot = $this->getObject('com:nucleonplus.model.slots')->id($slot->rt_slot_id)->fetch();

                $payout += $leftSlot->prpv;
                $payout += $rightSlot->prpv;

                $data[] = array(
                    'reward_id_from' => $leftSlot->reward_id,
                    'reward_id_to'   => $this->id,
                    'points'         => $leftSlot->prpv
                );
                $data[] = array(
                    'reward_id_from' => $rightSlot->reward_id,
                    'reward_id_to'   => $this->id,
                    'points'         => $rightSlot->prpv
                );
            }
        }

        // Ensure payout matches the expected amount of reward's product rebate pv x the binary of number of slots
        if ($requiredSlots == $payoutSlots)
        {
            $controller = $this->getObject('com:nucleonplus.controller.rebate');

            foreach ($data as $datum) {
                $controller->add($datum);
            }

            $this->status = 'ready';
            $this->save();
        }
    }

    /**
     * Prevent deletion of reward
     * A reward can only be voided but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }
}