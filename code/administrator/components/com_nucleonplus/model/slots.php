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
            ->insert('rebate_id', 'int')
            ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        /*$config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('package_name', 'account_number'))
            )
        ));*/

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns('rb.prpv')
            ->columns('rb.drpv')
            ->columns('rb.irpv')
            ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('rb' => 'nucleonplus_rebates'), 'tbl.rebate_id = rb.nucleonplus_rebate_id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->rebate_id) {
            $query->where('tbl.rebate_id = :rebate_id')->bind(['rebate_id' => $state->rebate_id]);
        }
    }

    /**
     * Get all unpaid slots i.e. slot with no left OR right leg
     *
     * @return array|null
     */
    public function getUnpaidSlots()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.slots');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_slots AS tbl')
            ->where('tbl.rebate_id != :rebate_id')->bind(['rebate_id' => $state->rebate_id])
            ->where('tbl.lf_slot_id = 0 OR tbl.rt_slot_id = 0')
        ;

        $slots = $table->select($query);

        // Double check that the member's slot will not be placed in his own slot since it is done in Rewardable::placeOwnSlots()
        if ($slots->rebate_id == $state->rebate_id) {
            return null;
        }

        // Determine which leg is available
        $slots->available_leg = ($slots->lf_slot_id == 0) ? 'lf_slot_id' : 'rt_slot_id';

        return $slots;
    }
}