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

class ComNucleonplusAccountingServiceInventory extends KObject implements ComNucleonplusAccountingServiceInventoryInterface
{
    /**
     * Result of inventory count for each item
     *
     * @var array
     */
    protected $_data;

    /**
     * Get quantity
     *
     * @param mixed   $id
     * @param boolean $detailed
     *
     * @return boolean
     */
    public function getQuantity($id, $detailed = false)
    {
        $item = $this->getObject('com://admin/qbsync.model.items')->ItemRef($id)->fetch();
        $available = ((int) $item->QtyOnHand - (int) $item->quantity_purchased);

        if ($detailed)
        {
            // Inventory item details
            $result = $item->getProperties();
            $result['available'] = $available;
        }
        else $result = $available;

        return $result;
    }
}
