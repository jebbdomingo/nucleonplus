<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerBehaviorMasspayable extends ComDragonpayControllerBehaviorMasspayable
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'actions'    => array('after.processing'),
            'columns'    => array(
                'merchantTxnId' => 'id',
                'userName'      => '_account_bank_account_name',
                'amount'        => 'amount',
                'procDetail'    => '_account_bank_account_number',
                'email'         => 'email',
                'mobileNo'      => '_account_mobile',
                'runDate'       => 'run_date',
            )
        ));

        parent::_initialize($config);
    }
}
