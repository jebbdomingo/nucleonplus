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
            ->columns('a.bank_account_number')
            ->columns('a.bank_account_name')
            ->columns('a.bank_account_type')
            ->columns('a.bank_account_branch')
            ->columns('a.phone')
            ->columns('a.mobile')
            ->columns('a.street')
            ->columns('a.city')
            ->columns('a.state')
            ->columns('a.postal_code')
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('a' => 'nucleonplus_accounts'), 'tbl.id = a.user_id')
        ;

        parent::_buildQueryJoins($query);
    }
}