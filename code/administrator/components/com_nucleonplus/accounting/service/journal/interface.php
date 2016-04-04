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
 * Journal Interface.
 *
 * @author Jebb Domingo <https://github.com/jebbdomingo>
 */
interface ComNucleonplusAccountingServiceJournalInterface
{
    /**
     * Record sale
     *
     * @param KModelEntityInterface $order
     *
     * @return mixed
     */
    public function recordSale(KModelEntityInterface $order);

    /**
     * Record rebates allocation
     *
     * @param KModelEntityInterface $slot
     *
     * @return mixed
     */
    public function recordRebatesAllocation(KModelEntityInterface $slot);

    /**
     * Record referral bonus allocation
     *
     * @param KModelEntityInterface $order
     *
     * @return mixed
     */
    public function recordReferralBonusAllocation(KModelEntityInterface $reward);
}