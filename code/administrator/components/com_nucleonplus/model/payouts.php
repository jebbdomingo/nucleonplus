<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelPayouts extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('account', 'string')
            ->insert('status', 'string')
            ->insert('search', 'string')
            ->insert('created_on', 'string')
            ->insert('payout_method', 'string')
        ;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_account_bank_account'      => '_account.bank_account_number'))
            ->columns(array('_account_bank_account_name' => '_account.bank_account_name'))
            ->columns(array('_account_mobile'            => '_account.mobile'))
            ->columns('_account.nucleonplus_account_id')
            ->columns('_account.status AS account_status')
            ->columns('_account.created_on AS account_created_on')
            ->columns('_user.name')
            ->columns('_user.email')
            ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_account' => 'nucleonplus_accounts'), 'tbl.account = _account.nucleonplus_account_id')
            ->join(array('_user' => 'users'), '_account.user_id = _user.id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->account) {
            $query->where('tbl.account = :account')->bind(['account' => $state->account]);
        }

        if ($state->status) {
            $query->where('tbl.status = :status')->bind(['status' => $state->status]);
        }

        if ($state->payout_method) {
            $query->where('tbl.payout_method = :payout_method')->bind(['payout_method' => $state->payout_method]);
        }

        if ($state->created_on) {
            $query->where('DATE_FORMAT(tbl.created_on,"%Y-%m-%d") = :created_on')->bind(['created_on' => $state->created_on]);
        }

        if ($state->search)
        {
            $conditions = array(
                '_account.account LIKE :keyword',
                '_user.name LIKE :keyword',
            );
            $query->where('(' . implode(' OR ', $conditions) . ')')->bind(['keyword' => "%{$state->search}%"]);
        }
    }

    /**
     * Set default sorting
     *
     * @param KModelContextInterface $context A model context object
     *
     * @return void
     */
    protected function _beforeFetch(KModelContextInterface $context)
    {
        if (is_null($context->state->sort)) {
            $context->query->order('_user.name', 'asc');
        }
    }

    public function getTotal()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_payouts AS tbl')
            ->columns('tbl.nucleonplus_payout_id, SUM(tbl.amount) AS total')
            ->group('tbl.payout_method')
        ;

        $this->_buildQueryWhere($query);

        $entities = $table->select($query);

        return (float) $entities->total;
    }

    public function hasOutstandingRequest()
    {
        $state = $this->getState();

        $status = array(
            ComNucleonplusModelEntityPayout::PAYOUT_STATUS_PENDING,
            ComNucleonplusModelEntityPayout::PAYOUT_STATUS_PROCESSING,
            ComNucleonplusModelEntityPayout::PAYOUT_TRANSFER_STATUS_PENDING,
            ComNucleonplusModelEntityPayout::PAYOUT_TRANSFER_STATUS_INPROGRESS,
        );

        $table = $this->getObject('com://admin/nucleonplus.database.table.payouts');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_payouts AS tbl')
            ->columns('tbl.nucleonplus_payout_id, COUNT(tbl.nucleonplus_payout_id) AS count')
            ->where('tbl.status IN :status')->bind(['status' => $status])
            ->where('tbl.account = :account')->bind(['account' => $state->account])
        ;

        $result = $table->select($query);
        $count  = (int) $result->count;

        return ($count > 0) ? true : false;
    }
}