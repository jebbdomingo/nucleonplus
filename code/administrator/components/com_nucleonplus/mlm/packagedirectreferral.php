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
 * Used by the order controller to create entries in the rewards system upon payment of order
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 */
class ComNucleonplusMlmPackagedirectreferral extends KObject
{
    /**
     * Create direct referral bonus
     *
     * @param KModelEntityInterface $slot
     *
     * @return void
     */
    public function create(KModelEntityInterface $slot)
    {
        // Pay direct referral bonus
        $account = $slot->getReward()->getAccount();

        if ((count($account->getLatestPurchases()) == 0) && $account->sponsor_id) {
            // Direct referrral bonus
            return $this->_connectToSponsor($account->getSponsor(), $slot);
        }

        return false;
    }

    /**
     * Direct referral bonus
     *
     * @param KModelEntityInterface $sponsor
     * @param KModelEntityInterface $slot
     *
     * @return booelan
     */
    private function _connectToSponsor(KModelEntityInterface $sponsor, KModelEntityInterface $slot)
    {
        $reward = $slot->getReward();

        $data = array(
            'reward_id' => $reward->id,
            'account_id' => $sponsor->id,
            'points' => $reward->prpv
        );

        $directReferral = $this->getObject('com:nucleonplus.model.directreferrals')->create($data);
        
        if ($directReferral->save())
        {
            $slot->consume();

            return true;
        }
        else return false;
    }
}
