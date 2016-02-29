<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelAccounts extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('status', 'string')
            ->insert('account_number', 'string')
            ->insert('sponsor_id', 'string')
            ->insert('user_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('status', 'account_number'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns('u.name')
            ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('u' => 'users'), 'tbl.user_id = u.id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if (!is_null($state->status) && $state->status <> 'all') {
            $query->where('(tbl.status IN :status)')->bind(array('status' => (array) $state->status));
        }

        if ($state->account_number) {
            $query->where('tbl.account_number = :account_number')->bind(['account_number' => $state->account_number]);
        }

        if ($state->sponsor_id) {
            $query->where('tbl.sponsor_id = :sponsor_id')->bind(['sponsor_id' => $state->sponsor_id]);
        }

        if ($state->user_id) {
            $query->where('tbl.user_id = :user_id')->bind(['user_id' => $state->user_id]);
        }
    }

    /**
     * Get total referral bonus per account
     * i.e. dr and ir bonuses
     *
     * @return KDatabaseRowsetDefault
     */
    public function getTotalReferralBonus()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.transactions');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_transactions AS tbl')
            ->columns('(SUM(tbl.credit) - SUM(tbl.debit)) AS total')
            ->where('tbl.account_id = :account_id')->bind(['account_id' => $state->id])
            ->where('tbl.reward_type IN :reward_type')->bind(['reward_type' => ['dr','ir']])
            ->group('tbl.account_id')
        ;

        return $table->select($query);
    }

    /**
     * Get total product rebates per account
     * i.e. pr
     *
     * @return KDatabaseRowsetDefault
     */
    public function getTotalRebates()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.transactions');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_transactions AS tbl')
            ->columns('(SUM(tbl.credit) - SUM(tbl.debit)) AS total')
            ->where('tbl.account_id = :account_id')->bind(['account_id' => $state->id])
            ->where('tbl.reward_type = :reward_type')->bind(['reward_type' => 'pr'])
            ->group('tbl.account_id')
        ;

        return $table->select($query);
    }
}