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
 * 
 * @author Jebb Domingo <https://github.com/jebbdomingo>
 */
interface ComNucleonplusAccountingServiceTransferInterface
{
    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocatePatronage($orderId, $amount);

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateSurplusPatronage($orderId, $amount);

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateDRBonus($orderId, $amount);

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateIRBonus($orderId, $amount);

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateSurplusDRBonus($orderId, $amount);

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateSurplusIRBonus($orderId, $amount);

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateCharges($orderId, $amount);

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateDeliveryExpense($orderId, $amount);
}