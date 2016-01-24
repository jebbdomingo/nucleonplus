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
            ->insert('product_id', 'int')
            ->insert('account_id', 'string')
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

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->product_id) {
            $query->where('tbl.product_id = :product_id')->bind(['product_id' => $state->product_id]);
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
            ->where('tbl.account_id != :account_id')->bind(['account_id' => $state->account_id])
            ->where('tbl.lf_slot_id IS NULL OR tbl.rt_slot_id IS NULL')
        ;

        $slots = $table->select($query);

        // Double check that the member's slot will not be placed in his own slot since it is done in Rewardable::placeOwnSlots()
        if ($slots->account_id == $state->account_id) {
            return null;
        }

        // Determine which leg is available
        $slots->available_leg = (is_null($slots->lf_slot_id)) ? 'lf_slot_id' : 'rt_slot_id';

        return $slots;
    }
}