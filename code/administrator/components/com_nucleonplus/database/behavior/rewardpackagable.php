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
class ComNucleonplusDatabaseBehaviorRewardpackagable extends KDatabaseBehaviorAbstract
{
    /**
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        $context->query
            ->columns(array('_rewardpackage_id'          => '_rewardpackage.nucleonplus_rewardpackage_id'))
            ->columns(array('_rewardpackage_name'        => '_rewardpackage.name'))
            ->columns(array('_rewardpackage_description' => '_rewardpackage.description'))
            ->columns(array('_rewardpackage_slots'       => '_rewardpackage.slots'))
            ->columns(array('_rewardpackage_prpv'        => '_rewardpackage.prpv'))
            ->columns(array('_rewardpackage_drpv'        => '_rewardpackage.drpv'))
            ->columns(array('_rewardpackage_irpv'        => '_rewardpackage.irpv'))
            ->join(array('_rewardpackage' => 'nucleonplus_rewardpackages'), 'tbl.rewardpackage_id = _rewardpackage.nucleonplus_rewardpackage_id')
        ;
    }
}