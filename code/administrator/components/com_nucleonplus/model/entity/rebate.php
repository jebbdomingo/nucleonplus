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
class ComNucleonplusModelEntityRebate extends KModelEntityRow
{
    /**
     * Process reward, check if this order is ready for payout
     *
     * @return boolean|void
     */
    public function processRebate()
    {
        if ($this->payout > 0) {
            return;
        }

        $slots  = $this->getObject('com:nucleonplus.model.slots')->rebate_id($this->id)->fetch();
        $payout = 0;
        
        foreach ($slots as $slot)
        {
            if ($slot->lf_slot_id == 0 || $slot->rt_slot_id == 0) {
                $payout = 0;
                break;
            } else {
                $leftSlot = $this->getObject('com:nucleonplus.model.slots')->id($slot->lf_slot_id)->fetch();
                $payout += $leftSlot->prpv;

                $rightSlot = $this->getObject('com:nucleonplus.model.slots')->id($slot->rt_slot_id)->fetch();
                $payout += $rightSlot->prpv;
            }
        }

        $this->payout = $payout;
        $this->save();
    }
}