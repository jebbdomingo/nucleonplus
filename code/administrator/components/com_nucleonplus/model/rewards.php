<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2917 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelRewards extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('type', 'string')
            ->insert('account', 'int')
            ->insert('payout_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                //'searchable' => array('columns' => array('product_id'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->type) {
            $query->where('tbl.type = :type')->bind(['type' => $state->type]);
        }

        if ($state->account) {
            $query->where('tbl.account = :account')->bind(['account' => $state->account]);
        }

        if ($state->payout_id === 0 || $state->payout_id > 0) {
            $query->where('tbl.payout_id = :payout_id')->bind(['payout_id' => $state->payout_id]);
        }
    }

    /**
     * Get direct referral bonus per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getDirectReferralBonus($account)
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.rewards');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_rewards AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.nucleonplus_reward_id')
            ->where('tbl.type IN :type')->bind(array('type' => array('direct_referral')))
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }

    /**
     * Get indirect referral bonus per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getIndirectReferralBonus($account)
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.rewards');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_rewards AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.nucleonplus_reward_id')
            ->where('tbl.type IN :type')->bind(array('type' => array('indirect_referral')))
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }

    /**
     * Get rebates per account
     *
     * @param string $account User account number
     * @return float
     */
    public function getRebates($account)
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.rewards');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_rewards AS tbl')
            ->columns('SUM(tbl.points) AS total, tbl.nucleonplus_reward_id')
            ->where('tbl.type IN :type')->bind(array('type' => array('rebates')))
            ->where('tbl.account = :account')->bind(array('account' => $account))
            ->group('tbl.account')
        ;

        $row = $table->select($query);

        return (float) $row->total;
    }
}