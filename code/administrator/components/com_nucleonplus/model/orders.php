<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelOrders extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('account_id', 'int')
            ->insert('order_status', 'string')
            ->insert('search', 'string')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                //'searchable' => array('columns' => array('nucleonplus_order_id', 'package_name', 'account_number', 'invoice_status'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns('_account.account_number')
            ->columns('_account.status')
            ->columns(array('_account_customer_ref' => '_account.CustomerRef'))
            ->columns('u.name')
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_account' => 'nucleonplus_accounts'), 'tbl.account_id = _account.nucleonplus_account_id')
            ->join(array('u' => 'users'), '_account.user_id = u.id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->account_id) {
            $query->where('tbl.account_id = :account_id')->bind(['account_id' => $state->account_id]);
        }

        if ($state->order_status && $state->order_status <> 'all') {
            $query->where('tbl.order_status = :order_status')->bind(['order_status' => $state->order_status]);
        }

        if ($state->search)
        {
            $conditions = array(
                '_account.account_number LIKE :keyword',
                'u.name LIKE :keyword',
            );
            $query->where('(' . implode(' OR ', $conditions) . ')')->bind(['keyword' => "%{$state->search}%"]);
        }
    }

    /**
     * Check if the member has existing order at this moment
     *
     * conditions: orders
     * - owned by the $accountId
     * - created today
     * - excluding cancelled and void orders
     *
     * @param [type] $accountId [description]
     *
     * @return boolean [description]
     */
    public function hasCurrentOrder($accountId)
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.orders');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_orders AS tbl')
            ->columns('COUNT(tbl.account_id) AS count')
            ->where('tbl.account_id = :account_id')->bind(['account_id' => $accountId])
            ->where('tbl.created_on >= :created_on')->bind(array('created_on' => date('Y-m-d')))
            ->where('tbl.order_status NOT IN :status')->bind(['status' => array('cancelled', 'void')])
            ->group('tbl.account_id')
        ;

        $entities = $table->select($query);

        return (intval($entities->count) > 0) ? true : false;
    }
}