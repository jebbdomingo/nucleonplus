<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelCarts extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('account_id', 'int')
            ->insert('package_id', 'int')
        ;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_package_name'               => '_package.name'))
            ->columns(array('_package_price'              => '_package.price'))
            ->columns(array('_package_rewardpackage_id'   => '_package.rewardpackage_id'))
            ->columns(array('_package_charges'            => '_package.charges'))
            ->columns(array('_package_shipping_packaging' => '_package.shipping_packaging'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_package' => 'nucleonplus_packages'), 'tbl.package_id = _package.nucleonplus_package_id', 'INNER')
        ;

        parent::_buildQueryJoins($query);
    }
}