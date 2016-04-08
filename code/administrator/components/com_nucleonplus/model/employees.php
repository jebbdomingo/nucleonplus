<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEmployees extends KModelDatabase
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('name'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns('_employee.DepartmentRef')
            ->columns('_employee.bank_account_number')
            ->columns('_employee.bank_account_name')
            ->columns('_employee.bank_account_type')
            ->columns('_employee.bank_account_branch')
            ->columns('_employee.phone')
            ->columns('_employee.mobile')
            ->columns('_employee.street')
            ->columns('_employee.city')
            ->columns('_employee.state')
            ->columns('_employee.postal_code')
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_employee' => 'nucleonplus_employees'), 'tbl.id = _employee.user_id', 'INNER')
        ;

        parent::_buildQueryJoins($query);
    }
}