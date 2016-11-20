<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */


/**
 * Cart Interface.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Nucleonplus\Component\Cart
 */
interface ComNucleonplusModelEntityCartInterface
{
    /**
     * Get shipping cost
     *
     * @return float
     */
    public function getShippingFee();

    /**
     * Get cart items
     *
     * @return KModelEntityRowset
     */
    public function getItems();

    /**
     * Get cart items amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * Get cart items sub-totalamount including shipping 
     *
     * @return float
     */
    public function getSubTotal();
}