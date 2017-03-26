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
            ->columns(array('_employee_given_name' => '_employee.given_name'))
            ->columns(array('_employee_family_name' => '_employee.family_name'))
            ->columns(array('_employee_check_name' => '_employee.PrintOnCheckName'))
            ->columns(array('_employee_department_ref' => '_employee.DepartmentRef'))
            ->columns(array('_employee_bank_account_number' => '_employee.bank_account_number'))
            ->columns(array('_employee_bank_account_name' => '_employee.bank_account_name'))
            ->columns(array('_employee_bank_account_type' => '_employee.bank_account_type'))
            ->columns(array('_employee_bank_account_branch' => '_employee.bank_account_branch'))
            ->columns(array('_employee_phone' => '_employee.phone'))
            ->columns(array('_employee_mobile' => '_employee.mobile'))
            ->columns(array('_employee_street' => '_employee.street'))
            ->columns(array('_employee_city' => '_employee.city'))
            ->columns(array('_employee_state' => '_employee.state'))
            ->columns(array('_employee_postal_code'  => '_employee.postal_code'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_employee' => 'nucleonplus_employeeaccounts'), 'tbl.id = _employee.user_id', 'INNER')
        ;

        parent::_buildQueryJoins($query);
    }
}