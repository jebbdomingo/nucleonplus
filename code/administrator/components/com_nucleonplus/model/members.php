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
            ->columns(array('_account_number'              => '_account.nucleonplus_account_id'))
            ->columns(array('_account_check_name'          => '_account.PrintOnCheckName'))
            ->columns(array('_account_customer_ref'        => '_account.CustomerRef'))
            ->columns(array('_account_status'              => '_account.status'))
            ->columns(array('_account_sponsor_id'          => '_account.sponsor_id'))
            ->columns(array('_account_bank_name'           => '_account.bank_name'))
            ->columns(array('_account_bank_account_number' => '_account.bank_account_number'))
            ->columns(array('_account_bank_account_name'   => '_account.bank_account_name'))
            ->columns(array('_account_bank_account_type'   => '_account.bank_account_type'))
            ->columns(array('_account_bank_account_branch' => '_account.bank_account_branch'))
            ->columns(array('_account_phone'               => '_account.phone'))
            ->columns(array('_account_mobile'              => '_account.mobile'))
            ->columns(array('_account_street'              => '_account.street'))
            ->columns(array('_account_postal_code'         => '_account.postal_code'))
            ->columns(array('city_id'                      => '_account.city'))
            ->columns(array('city'                         => "_city.name"))
            ->columns(array('province'                     => "_province.name"))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_account' => 'nucleonplus_accounts'), 'tbl.id = _account.user_id')
            ->join(array('_city' => 'nucleonplus_cities'), '_account.city = _city.nucleonplus_city_id')
            ->join(array('_province' => 'nucleonplus_provinces'), '_city.province_id = _province.nucleonplus_province_id')
        ;

        parent::_buildQueryJoins($query);
    }
}