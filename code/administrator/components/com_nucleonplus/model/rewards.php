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

        if ($state->product_id) {
            $query->where('tbl.product_id = :product_id')->bind(['product_id' => $state->product_id]);
        }

        if ($state->customer_id) {
            $query->where('tbl.customer_id) = :customer_id)')->bind(['customer_id)' => $state->customer_id]);
        }
    }

    public function getRebates()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.rewards');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_rewards AS tbl')
            ->columns('tbl.*, SUM(r.points) AS total')
            ->join(array('r' => 'nucleonplus_rebates'), 'tbl.nucleonplus_reward_id = r.reward_id_to')
            ->where('tbl.customer_id = :customer_id')->bind(['customer_id' => $state->customer_id])
            ->where('tbl.status = :status')->bind(['status' => $state->status])
            ->group('r.reward_id_to')
        ;

        return $table->select($query);
    }

    /**
     * Get rebates by rewards
     *
     * @param int $id
     *
     * @return KModelEntityInterface
     */
    public function getRebatesByReward($id)
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.rebates');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_rebates AS tbl')
            ->columns('tbl.*')
            ->join(array('r' => 'nucleonplus_rewards'), 'r.nucleonplus_reward_id = tbl.reward_id_to')
            ->where('tbl.reward_id_to = :reward_id')->bind(['reward_id' => $id])
            ->where('r.customer_id = :customer_id')->bind(['customer_id' => $state->customer_id])
            ->where('r.status = :status')->bind(['status' => $state->status])
        ;

        return $table->select($query);
    }
}