<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusDatabaseBehaviorShippable extends KDatabaseBehaviorAbstract
{
    /**
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        $context->query
            ->columns(array('city' => "CONCAT(_city.name, ', ', _province.name)"))
            ->columns(array('city_id' => 'tbl.city'))
            ->join(array('_city' => 'nucleonplus_cities'), 'tbl.city = _city.nucleonplus_city_id')
            ->join(array('_province' => 'nucleonplus_provinces'), '_city.province_id = _province.nucleonplus_province_id')
        ;
    }
}
