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
 * Item Interface.
 *
 * @author Jebb Domingo <https://github.com/jebbdomingo>
 */
interface ComNucleonplusAccountingServiceInventoryInterface
{
    /**
     * Decrease an item's quantity
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    public function decreaseQuantity(KModelEntityInterface $order);
}