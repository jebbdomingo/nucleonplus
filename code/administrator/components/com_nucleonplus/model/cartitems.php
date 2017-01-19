<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelCartitems extends ComCartModelItems
{
    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_item_id'           => '_item.qbsync_item_id'))
            ->columns(array('_item_ref'           => '_item.ItemRef'))
            ->columns(array('_item_type'          => '_item.Type'))
            ->columns(array('_item_name'          => '_item.Name'))
            ->columns(array('_item_price'         => '_item.UnitPrice'))
            ->columns(array('_item_image'         => '_item.image'))
            ->columns(array('_item_description'   => '_item.Description'))
            ->columns(array('_item_qty_onhand'    => '_item.QtyOnHand'))
            ->columns(array('_item_qty_purchased' => '_item.quantity_purchased'))
            ->columns(array('_item_shipping_type' => '_item.shipping_type'))
            ->columns(array('_item_weight' => '_item.weight'))
        ;
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_item' => 'qbsync_items'), 'tbl.row = _item.ItemRef')
        ;

        parent::_buildQueryJoins($query);
    }
}
