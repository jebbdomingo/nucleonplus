<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusDatabaseTableDragonpays extends KDatabaseTableAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'name'    => 'nucleonplus_orders',
            'filters' => array(
            )
        ));
        
        parent::_initialize($config);
    }
}