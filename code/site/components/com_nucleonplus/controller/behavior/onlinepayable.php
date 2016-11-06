<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerBehaviorOnlinepayable extends ComDragonpayControllerBehaviorOnlinepayable
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => self::PRIORITY_LOWEST,
            'actions'    => array('after.add'),
            'columns'    => array(
                'txnid'  => 'id',
                'amount' => 'total',
                'mode'   => 'payment_mode',
            )
        ));

        parent::_initialize($config);
    }
}
