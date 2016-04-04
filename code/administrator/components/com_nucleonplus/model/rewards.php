<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelRewards extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('status', 'string')
            ->insert('product_id', 'int')
            ->insert('customer_id', 'int')
            ->insert('payout_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('status', 'product_id'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->status) {
            $query->where('tbl.status = :status')->bind(['status' => $state->status]);
        }

        if ($state->product_id) {
            $query->where('tbl.product_id = :product_id')->bind(['product_id' => $state->product_id]);
        }

        if ($state->customer_id) {
            $query->where('tbl.customer_id) = :customer_id)')->bind(['customer_id)' => $state->customer_id]);
        }

        if ($state->payout_id === 0 || $state->payout_id > 0) {
            $query->where('tbl.payout_id = :payout_id')->bind(['payout_id' => $state->payout_id]);
        }
    }
}