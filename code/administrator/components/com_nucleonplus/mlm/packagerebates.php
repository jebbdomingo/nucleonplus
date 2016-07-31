<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmPackagerebates extends KObject
{
    /**
     * Create direct referral bonus
     *
     * @param KModelEntityInterface $slot
     *
     * @return void
     */
    public function create(KModelEntityInterface $reward)
    {
        return $this->_createRebates($reward);
    }

    /**
     * Direct referral bonus
     *
     * @param KModelEntityInterface $sponsor
     * @param KModelEntityInterface $slot
     *
     * @return booelan
     */
    private function _createRebates(KModelEntityInterface $reward)
    {
        $rebates = $this->getObject('com:nucleonplus.model.rebates')->create(array(
            'reward_id'  => $reward->id,
            'account_id' => $reward->getAccount()->id,
            'points'     => $reward->rebates
        ));
        
        return $rebates->save();
    }
}
