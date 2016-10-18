<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelCartitems extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('cart_id', 'int')
        ;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_package_name'  => '_package.name'))
            ->columns(array('_package_price' => '_package.price'))
            // ->columns(array('_package_price' => 'SUM(_item.price * _package_item.quantity)'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_package' => 'nucleonplus_packages'), 'tbl.package_id = _package.nucleonplus_package_id')
            // ->join(array('_package_item' => 'nucleonplus_packageitems'), '_package.nucleonplus_package_id = _package_item.package_id')
            // ->join(array('_item' => 'nucleonplus_items'), '_package_item.item_id = _item.nucleonplus_item_id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->cart_id) {
            $query->where('tbl.cart_id = :cart_id')->bind(['cart_id' => $state->cart_id]);
        }
    }

    // protected function _buildQueryGroup(KDatabaseQueryInterface $query)
    // {
    //     parent::_buildQueryGroup($query);

    //     $query->group('tbl.package_id');
    // }
}
