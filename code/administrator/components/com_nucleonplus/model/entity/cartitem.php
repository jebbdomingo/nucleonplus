<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityCartitem extends KModelEntityRow
{
    /**
     * Check available stock against this cart item quantity
     *
     * @return boolean
     */
    public function hasAvailableStock()
    {
        $result = false;

        if ($this->_item_type == 'Group')
        {
            // Query grouped items
            $items = $this->getObject('com://admin/qbsync.model.itemgroups')->parent_id($this->_item_ref)->fetch();

            foreach ($items as $item)
            {
                $inventoryQty = ((int) $item->_item_qty_onhand - (int) $item->_item_qty_purchased);

                if ($this->quantity < $inventoryQty) {
                    $result = true;
                }
            }
        }
        else
        {
            $inventoryQty = ((int) $this->_item_qty_onhand - (int) $this->_item_qty_purchased);

            if ($this->quantity < $inventoryQty) {
                $result = true;
            }
        }


        return $result;
    }
}
