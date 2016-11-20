<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusDatabaseTableCarts extends ComCartDatabaseTableCarts
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'locatable',
                'com:xend.database.behavior.shippable',
                'com:dragonpay.database.behavior.onlinepayable',
            )
        ));
        
        parent::_initialize($config);
    }
}
