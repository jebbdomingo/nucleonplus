<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityPackageitem extends KModelEntityRow
{
    /**
     * Prevent deletion of order
     * An order can only be void but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Check available stock against this package quantity
     *
     * @return boolean
     */
    public function hasAvailableStock()
    {
        $inventoryQty = ($this->_syncitem_qty_on_hand - $this->_syncitem_quantity_purchased);

        if ($this->quantity > $inventoryQty) {
            return false;
        }

        return true;
    }
}