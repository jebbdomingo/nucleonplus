<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelSlots extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('reward_id', 'int')
            ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns('r.product_id')
            ->columns('r.customer_id')
            ->columns('r.slots')
            ->columns('r.prpv')
            ->columns('r.drpv')
            ->columns('r.irpv')
            ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('r' => 'nucleonplus_rewards'), 'tbl.reward_id = r.nucleonplus_reward_id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->reward_id) {
            $query->where('tbl.reward_id = :reward_id')->bind(['reward_id' => $state->reward_id]);
        }
    }

    /**
     * Get all unpaid slots i.e. slot with no left OR right leg
     *
     * @return array|null
     */
    public function getUnpaidSlots($reward_id)
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.slots');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_slots AS tbl')
            ->where('tbl.reward_id != :reward_id')->bind(['reward_id' => $reward_id])
            ->where('tbl.lf_slot_id = 0 OR tbl.rt_slot_id = 0')
        ;

        $slots = $table->select($query);

        // Double check that the member's slot will not be placed in his own slot since it is done in Rewardable::placeOwnSlots()
        // TODO find another way how to do this properly
        if ($slots->reward_id == $state->reward_id) {
            return null;
        }

        // Determine which leg is available
        $slots->available_leg = ($slots->lf_slot_id == 0) ? 'lf_slot_id' : 'rt_slot_id';

        return $slots;
    }
}