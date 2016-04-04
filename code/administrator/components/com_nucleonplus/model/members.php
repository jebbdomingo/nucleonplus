<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelMembers extends KModelDatabase
{
    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns('_account.bank_account_number')
            ->columns('_account.bank_account_name')
            ->columns('_account.bank_account_type')
            ->columns('_account.bank_account_branch')
            ->columns('_account.phone')
            ->columns('_account.mobile')
            ->columns('_account.street')
            ->columns('_account.city')
            ->columns('_account.state')
            ->columns('_account.postal_code')
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_account' => 'nucleonplus_accounts'), 'tbl.id = _account.user_id')
        ;

        parent::_buildQueryJoins($query);
    }
}