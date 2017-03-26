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
 * Inventory Quantity Checker Interface.
 *
 * @author Jebb Domingo <https://github.com/jebbdomingo>
 */
interface ComNucleonplusAccountingServiceInventoryInterface
{
    /**
     * Get quantity
     *
     * @param mixed   $id
     * @param boolean $detailed
     *
     * @return boolean
     */
    public function getQuantity($id, $detailed = false);
}