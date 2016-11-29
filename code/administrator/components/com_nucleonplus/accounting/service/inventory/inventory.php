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
                if ($groupedItem->_item_type == ComQbsyncModelEntityItem::TYPE_INVENTORY_ITEM)
                {
                    if (!$this->_checkQuantity(($quantity * $groupedItem->quantity), $groupedItem->_item_qty_onhand, $groupedItem->_item_qty_purchased))
                    {
                        $result = false;
                        break;
                    }
                    else $result = true;
                }
            }
        }
        else $result = $this->_checkQuantity($quantity, $item->QtyOnHand, $item->quantity_purchased);

        return $result;
    }

    protected function _checkQuantity($quantity, $onHand, $purchases)
    {
        $result = false;

        $inventoryQty = ((int) $onHand - (int) $purchases);

        if ($quantity <= $inventoryQty) {
            $result = true;
        }

        return $result;
    }
}
