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
 * Slot Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
 */
class ComNucleonplusModelEntitySlot extends KModelEntityRow
{
    /**
     * Consume
     *
     * @return boolean
     */
    public function consume()
    {
        $this->consumed = 1;
        
        return $this->save();
    }

    /**
     * Prevent deletion of slot
     * A slot can only be voided but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Get the reward
     *
     * @return KModelEntityInterface
     */
    public function getReward()
    {
        return $this->getObject('com:nucleonplus.model.rewards')->id($this->reward_id)->fetch();
    }
}