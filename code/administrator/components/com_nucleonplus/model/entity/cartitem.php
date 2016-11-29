<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityCartitem extends ComCartModelEntityItem
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
                if ($item->_item_type == self::TYPE_INVENTORY_ITEM)
                {
                    if (!$this->_checkQuantity(($this->quantity * $item->quantity), $item->_item_qty_onhand, $item->_item_qty_purchased))
                    {
                        $result = false;
                        break;
                    }
                    else $result = true;
                }
            }
        }
        else $result = $this->_checkQuantity($this->quantity, $this->_item_qty_onhand, $this->_item_qty_purchased);

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
