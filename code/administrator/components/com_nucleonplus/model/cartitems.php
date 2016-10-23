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
            ->columns(array('_item_ref'           => '_item.ItemRef'))
            ->columns(array('_item_type'          => '_item.Type'))
            ->columns(array('_item_name'          => '_item.Name'))
            ->columns(array('_item_price'         => '_item.UnitPrice'))
            ->columns(array('_item_description'   => '_item.Description'))
            ->columns(array('_item_qty_onhand'    => '_item.QtyOnHand'))
            ->columns(array('_item_qty_purchased' => '_item.quantity_purchased'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_item' => 'qbsync_items'), 'tbl.ItemRef = _item.ItemRef')
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
}
