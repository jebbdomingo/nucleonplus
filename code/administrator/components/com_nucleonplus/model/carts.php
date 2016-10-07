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
            ->insert('cart_id', 'int')
            ->insert('package_id', 'int')
        ;
    }

    /**
     * Get the total amount of this cart
     *
     * @return decimal
     */
    public function getAmount()
    {
        $state = $this->getState();

        $table = $this->getObject('com://admin/nucleonplus.database.table.carts');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_carts AS tbl')
            ->columns('tbl.nucleonplus_cart_id, SUM(_items.price * _package_items.quantity * _cart_items.quantity) AS total')
            ->join(array('_cart_items' => 'nucleonplus_cartitems'), '_cart_items.cart_id = tbl.nucleonplus_cart_id', 'INNER')
            ->join(array('_packages' => 'nucleonplus_packages'), '_cart_items.package_id = _packages.nucleonplus_package_id', 'INNER')
            ->join(array('_package_items' => 'nucleonplus_packageitems'), '_package_items.package_id = _packages.nucleonplus_package_id', 'INNER')
            ->join(array('_items' => 'nucleonplus_items'), '_items.nucleonplus_item_id = _package_items.item_id', 'INNER')
            ->where('tbl.account_id = :account_id')->bind(['account_id' => $state->account_id])
            ->group('tbl.account_id')
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

        $table = $this->getObject('com://admin/nucleonplus.database.table.cartitems');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_cartitems AS tbl')
            ->columns('tbl.nucleonplus_cartitem_id, SUM(_items.weight * _package_items.quantity * tbl.quantity) AS total')
            ->join(array('_packages' => 'nucleonplus_packages'), 'tbl.package_id = _packages.nucleonplus_package_id', 'INNER')
            ->join(array('_package_items' => 'nucleonplus_packageitems'), '_package_items.package_id = _packages.nucleonplus_package_id', 'INNER')
            ->join(array('_items' => 'nucleonplus_items'), '_items.nucleonplus_item_id = _package_items.item_id', 'INNER')
            ->where('tbl.cart_id = :cart_id')->bind(['cart_id' => $state->cart_id])
            ->group('tbl.cart_id')
        ;

        $entities = $table->select($query);

        return $entities->total;
    }
}
