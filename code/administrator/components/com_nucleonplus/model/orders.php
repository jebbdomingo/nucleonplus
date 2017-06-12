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
            ->insert('payment_method', 'string')
            ->insert('search', 'string')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('nucleonplus_order_id'))
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
            ->columns(array('_user_email' => 'u.email'))
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
            $query->where('tbl.order_status IN :order_status')->bind(['order_status' => (array) $state->order_status]);
        }

        if ($state->search)
        {
            $conditions = array(
                'tbl.nucleonplus_order_id LIKE :keyword'
            );
            $query->where('(' . implode(' OR ', $conditions) . ')')->bind(['keyword' => "%{$state->search}%"]);
        }

        if ($state->payment_method) {
            $query->where('tbl.payment_method IN :payment_method')->bind(['payment_method' => (array) $state->payment_method]);
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
            $context->query->order('tbl.nucleonplus_order_id', 'desc');
        }
    }

    /**
     * Get the total amount of this order
     *
     * @return decimal
     */
    public function getAmount()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.orderitems');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_orderitems AS tbl')
            ->columns('tbl.nucleonplus_orderitem_id, SUM(tbl.item_price * tbl.quantity) AS total')
            ->where('tbl.order_id = :order_id')->bind(['order_id' => $state->id])
            ->group('tbl.order_id')
        ;

        $entities = $table->select($query);

        return (float) $entities->total;
    }
}