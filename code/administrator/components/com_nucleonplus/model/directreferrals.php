<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelDirectreferrals extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('reward_id', 'int')
            ->insert('account_id', 'int')
            ->insert('payout_id', 'int')
        ;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('reward_product_id' => '_reward.product_id'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_reward' => 'nucleonplus_rewards'), 'tbl.reward_id = _reward.nucleonplus_reward_id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->reward_id) {
            $query->where('tbl.reward_id IN :reward_id')->bind(['reward_id' => (array) $state->reward_id]);
        }

        if ($state->account_id) {
            $query->where('tbl.account_id IN :account_id')->bind(['account_id' => (array) $state->account_id]);
        }

        if ($state->payout_id === 0 || $state->payout_id > 0) {
            $query->where('tbl.payout_id IN :payout_id')->bind(['payout_id' => (array) $state->payout_id]);
        }
    }
}