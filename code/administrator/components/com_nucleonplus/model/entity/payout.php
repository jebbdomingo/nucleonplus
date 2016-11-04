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

class ComNucleonplusModelEntityPayout extends KModelEntityRow
{
    const PAYOUT_METHOD_PICKUP         = 'pickup';
    const PAYOUT_METHOD_FUNDS_TRANSFER = 'funds_transfer';

    const PAYOUT_STATUS_PENDING         = 'pending';
    const PAYOUT_STATUS_PROCESSING      = 'processing';
    const PAYOUT_STATUS_CHECK_GENERATED = 'checkgenerated';
    const PAYOUT_STATUS_DISBURSED       = 'disbursed';
    
    const PAYOUT_TRANSFER_STATUS_SUCCESS    = 'S';
    const PAYOUT_TRANSFER_STATUS_FAILED     = 'F';
    const PAYOUT_TRANSFER_STATUS_PENDING    = 'P';
    const PAYOUT_TRANSFER_STATUS_INPROGRESS = 'G';
    const PAYOUT_TRANSFER_STATUS_VOIDED     = 'V';

    /**
     * Prevent deletion of payout
     * A payout can only be voided but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }
}