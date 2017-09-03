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
            ->insert('sponsor_id', 'string')
            ->insert('user_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('nucleonplus_account_id', 'user_name'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_name' => '_user.name'))
            ->columns(array('_email' => '_user.email'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_user' => 'users'), 'tbl.user_id = _user.id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        // if (!is_null($state->status) && $state->status <> 'all') {
        //     $query->where('tbl.status = :status')->bind(['status' => $state->status]);
        // }

        if (is_null($state->status)) {
            $query->where('(tbl.status != :status)')->bind(array('status' => ComNucleonplusModelEntityAccount::STATUS_DELETED));
        } else {
            $query->where('(tbl.status IN :status)')->bind(array('status' => (array) $state->status));
        }

        if ($state->sponsor_id) {
            $query->where('tbl.sponsor_id = :sponsor_id')->bind(['sponsor_id' => $state->sponsor_id]);
        }

        if ($state->user_id) {
            $query->where('tbl.user_id = :user_id')->bind(['user_id' => $state->user_id]);
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
            $context->query->order('tbl.nucleonplus_account_id', 'desc');
        }
    }
}