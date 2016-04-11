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

/**
 * @todo Implement a local queue of accounting/inventory transactions in case of trouble connecting to accounting system
 */
class ComNucleonplusAccountingServiceInventory extends ComNucleonplusAccountingServiceObject implements ComNucleonplusAccountingServiceInventoryInterface
{
    /**
     * Decrease item quantity
     *
     * @param KModelEntityInterface $order
     *
     * @return resource
     */
    /*public function decreaseQuantity(KModelEntityInterface $order)
    {
        foreach ($order->getItems() as $item)
        {
            $inventoryItem = $this->find($item->inventory_item_id);

            $oldQty = $inventoryItem->getQtyOnHand();
            $newQty = ($inventoryItem->getQtyOnHand() - $item->quantity);

            // Update the item's quantity
            $inventoryItem->setQtyOnHand($newQty);
        }

        return $this->update($inventoryItem);
    }*/

    /**
     * Get an item
     *
     * @param integer $id
     * 
     * @return object
     */
    public function find($id)
    {
        // Item
        $itemService = new QuickBooks_IPP_Service_Term();

        $items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '{$id}' ");

        if (count($items) == 0) {
            return null;
        }

        return $items[0];
    }

    /**
     * Update item inventory quantity
     *
     * @param array $data
     *
     * @throws Exception
     * 
     * @return  void
     */
    /*public function update($item)
    {
        $itemService = new QuickBooks_IPP_Service_Item();

        if ($res = $itemService->update($this->Context, $this->realm, $item->getId(), $item)) {
            return $res;
        }
        else throw new Exception($itemService->lastError($this->Context));
    }*/
}