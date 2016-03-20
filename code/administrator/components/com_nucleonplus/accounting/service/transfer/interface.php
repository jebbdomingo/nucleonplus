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
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateRebates($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateSurplusRebates($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateDRBonus($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateIRBonus($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateSurplusDRBonus($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateSurplusIRBonus($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateSystemFee($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateContingencyFund($amount);

    /**
     *
     * @param decimal $amount
     *
     * @return mixed
     */
    public function allocateOperationsFund($amount);
}