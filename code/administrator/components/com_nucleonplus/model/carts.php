<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelCarts extends ComCartModelCarts
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('interface', 'string')
        ;
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->interface) {
            $query->where('tbl.interface = :interface')->bind(['interface' => $state->interface]);
        }
    }

    /**
     * Get the total amount of this cart
     *
     * @return decimal
     */
    public function getAmount()
    {
        $state = $this->getState();

        $table = $this->getObject('com:cart.database.table.carts');
        $query = $this->getObject('database.query.select')
            ->table('carts AS tbl')
            ->columns('tbl.cart_id, SUM(_item.UnitPrice * _cart_items.quantity) AS total')
            ->join(array('_cart_items' => 'cart_items'), '_cart_items.cart_id = tbl.cart_id', 'INNER')
            ->join(array('_item' => 'qbsync_items'), '_cart_items.row = _item.ItemRef', 'INNER')
            ->where('tbl.customer = :customer')->bind(['customer' => $state->customer])
            ->group('tbl.customer')
        ;

        $row = $table->select($query);

        return $row->total;
    }

    /**
     * Get the total weight of this order
     *
     * @return integer
     */
    public function getWeight()
    {
        $state = $this->getState();

        $table = $this->getObject('com:cart.database.table.items');
        $query = $this->getObject('database.query.select')
            ->table('cart_items AS tbl')
            ->columns('tbl.cart_item_id, SUM(_item.weight * tbl.quantity) AS total')
            ->join(array('_item' => 'qbsync_items'), 'tbl.row = _item.ItemRef', 'INNER')
            ->where('tbl.cart_id = :cart_id')->bind(['cart_id' => $state->cart_id])
            ->group('tbl.cart_id')
        ;

        $entities = $table->select($query);

        return (int) $entities->total;
    }
}
