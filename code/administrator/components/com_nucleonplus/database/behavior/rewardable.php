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
 * Rewardable Database Behavior
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Nucleonplus\Database\Behavior
 */
class ComNucleonplusDatabaseBehaviorRewardable extends KDatabaseBehaviorAbstract
{
    /**
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        $context->query
            ->columns(array('_reward_id'           => '_reward.nucleonplus_reward_id'))
            ->columns(array('_reward_customer_id'  => '_reward.customer_id'))
            ->columns(array('_reward_package_id'   => '_reward.rewardpackage_id'))
            ->columns(array('_reward_product_name' => '_reward.product_name'))
            ->columns(array('_reward_status'       => '_reward.status'))
            ->columns(array('_reward_slots'        => '_reward.slots'))
            ->columns(array('_reward_prpv'         => '_reward.prpv'))
            ->columns(array('_reward_drpv'         => '_reward.drpv'))
            ->columns(array('_reward_irpv'         => '_reward.irpv'))
            ->join(array('_reward' => 'nucleonplus_rewards'), 'tbl.nucleonplus_order_id = _reward.product_id')
        ;
    }
}