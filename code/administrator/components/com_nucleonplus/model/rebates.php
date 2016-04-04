<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelRebates extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('customer_id', 'int')
            ->insert('status', 'string')
            ->insert('payout_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                // 'searchable' => array('columns' => array('status', 'product_id'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query->columns('r.*');
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->customer_id) {
            $query->where('r.customer_id = :customer_id')->bind(['customer_id' => $state->customer_id]);
        }

        if ($state->status) {
            $query->where('r.status = :status')->bind(['status' => $state->status]);
        }

        if ($state->payout_id === 0 || $state->payout_id > 0) {
            $query->where('r.payout_id = :payout_id')->bind(['payout_id' => $state->payout_id]);
        }
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('r' => 'nucleonplus_rewards'), 'tbl.reward_id_to = r.nucleonplus_reward_id')
        ;

        parent::_buildQueryJoins($query);
    }
}