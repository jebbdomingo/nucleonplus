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
 * Rebatable Database Behavior
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Nucleonplus\Database\Behavior
 */
class ComNucleonplusDatabaseBehaviorRebatable extends KDatabaseBehaviorAbstract
{
    /**
     * Set created information
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        $context->query
            ->columns(array('_rebate_id'          => '_rebate.nucleonplus_rebate_id'))
            ->columns(array('_rebate_customer_id' => '_rebate.customer_id'))
            ->columns(array('_rebate_reward_id'   => '_rebate.reward_id'))
            ->columns(array('_rebate_status'      => '_rebate.status'))
            ->columns(array('_rebate_slots'       => '_rebate.slots'))
            ->columns(array('_rebate_prpv'        => '_rebate.prpv'))
            ->columns(array('_rebate_drpv'        => '_rebate.drpv'))
            ->columns(array('_rebate_irpv'        => '_rebate.irpv'))
            ->join(array('_rebate' => 'nucleonplus_rebates'), 'tbl.nucleonplus_order_id = _rebate.product_id')
        ;
    }
}