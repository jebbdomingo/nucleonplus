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
                'searchable' => array('columns' => array('account_number'))
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

        if (!is_null($state->status) && $state->status <> 'all') {
            $query->where('tbl.status = :status')->bind(['status' => $state->status]);
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
     * Get total available referral bonus per account
     * i.e. dr and ir bonuses
     *
     * @return KDatabaseRowsetDefault
     */
    public function getTotalAvailableReferralBonus()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.referralbonuses');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_referralbonuses AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.nucleonplus_referralbonus_id')
            ->where('tbl.account_id = :account_id')->bind(['account_id' => $state->id])
            ->where('tbl.referral_type IN :referral_type')->bind(['referral_type' => ['dr','ir']])
            ->where('tbl.payout_id = :payout_id')->bind(['payout_id' => 0])
            ->group('tbl.account_id')
        ;

        return $table->select($query);
    }

    /**
     * Get total available product rebates per account
     * i.e. pr
     *
     * @return KDatabaseRowsetDefault
     */
    public function getTotalAvailableRebates()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.rebates');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_rebates AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.nucleonplus_rebate_id')
            ->join(array('r' => 'nucleonplus_rewards'), 'tbl.reward_id_to = r.nucleonplus_reward_id')
            ->where('r.customer_id = :account_id')->bind(['account_id' => $state->id])
            ->where('r.status = :status')->bind(['status' => 'ready'])
            ->where('r.payout_id = :payout_id')->bind(['payout_id' => 0])
            ->group('r.customer_id')
        ;

        return $table->select($query);
    }
}