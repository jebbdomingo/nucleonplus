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
 * Member's Patronage Bonus Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
 */
class ComNucleonplusModelEntityReward extends KModelEntityRow
{
    const REWARD_PACKAGE = 'Group';
    const REWARD_RETAIL  = 'Inventory';
    const STATUS_ACTIVE  = 'active';
    const STATUS_READY   = 'ready';
    const STATUS_CLAIMED = 'claimed';

    /**
     * Process member's patronage bonus
     *
     * @return boolean|void
     */
    public function processPatronage()
    {
        if ($this->status <> self::STATUS_ACTIVE) {
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

        // Ensure payout matches the expected amount of reward's product patronage bonus pv x the binary of number of slots
        if ($requiredSlots === $payoutSlots)
        {
            $model = $this->getObject('com:nucleonplus.model.patronagebonuses');

            foreach ($data as $datum)
            {
                $entity = $model->create($datum);
                $entity->save();
            }

            $this->status = self::STATUS_READY;
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

    public function getAccount()
    {
        return $this->getObject('com:nucleonplus.model.accounts')->id($this->customer_id)->fetch();
    }
}
