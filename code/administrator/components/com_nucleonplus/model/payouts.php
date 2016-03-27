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
            ->insert('account_id', 'int')
            ->insert('status', 'string')
            ->insert('search', 'string')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                // 'searchable' => array('columns' => array('account_number', 'status'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns('a.account_number')
            ->columns('a.status AS account_status')
            ->columns('a.created_on AS account_created_on')
            ->columns('u.name')
            ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('a' => 'nucleonplus_accounts'), 'tbl.account_id = a.nucleonplus_account_id')
            ->join(array('u' => 'users'), 'a.user_id = u.id')
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

        if ($state->status && $state->status <> 'all') {
            $query->where('tbl.status = :status')->bind(['status' => $state->status]);
        }

        if ($state->search)
        {
            $conditions = array(
                'a.account_number LIKE :keyword',
                'u.name LIKE :keyword',
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
            $context->query->order('u.name', 'asc');
        }
    }
}