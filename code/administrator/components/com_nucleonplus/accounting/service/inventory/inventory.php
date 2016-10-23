<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusAccountingServiceInventory extends KObject
{
    /**
     * Check available stock
     *
     * @param integer $itemRef
     * @param integer $quantity
     *
     * @return boolean
     */
    public function hasAvailableStock($itemRef, $quantity)
    {
        $result = false;
        $item   = $this->getObject('com://admin/qbsync.model.items')->ItemRef($itemRef)->fetch();

        if ($item->Type == 'Group')
        {
            // Query grouped items
            $groupedItems = $this->getObject('com://admin/qbsync.model.itemgroups')->parent_id($itemRef)->fetch();

            foreach ($groupedItems as $groupedItem)
            {
                $inventoryQty = ((int) $groupedItem->_item_qty_onhand - (int) $groupedItem->_item_qty_purchased);

                if ($quantity < $inventoryQty) {
                    $result = true;
                }
            }
        }
        else
        {
            $inventoryQty = ((int) $item->QtyOnHand - (int) $item->quantity_purchased);

            if ($quantity < $inventoryQty) {
                $result = true;
            }
        }

        return $result;
    }
}
