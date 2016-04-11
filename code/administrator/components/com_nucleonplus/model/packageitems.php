<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelPackageitems extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('package_id', 'int')
        ;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_item_name' => '_item.name'))
            ->columns(array('_item_price' => '_item.price'))
            ->columns(array('_qboitem_itemref' => '_qboitem.ItemRef'))
            ->columns(array('_qboitem_unitprice' => '_qboitem.UnitPrice'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_item' => 'nucleonplus_items'), 'tbl.item_id = _item.nucleonplus_item_id')
            ->join(array('_qboitem' => 'nucleonplus_qboitems'), '_item.nucleonplus_item_id = _qboitem.item_id')
        ;

        parent::_buildQueryJoins($query);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->package_id) {
            $query->where('(tbl.package_id = :package_id)')->bind(array('package_id' => $state->package_id));
        }
    }
}