<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityConfig extends KModelEntityRow
{
    const PAYOUT_CLAIM_REQUEST_NAME = 'claim_request';
    const PAYOUT_CLAIM_REQUEST_ID   = 1;
    const CLAIM_REQUEST_ENABLED     = 'enabled';
    const CLAIM_REQUEST_DISABLED    = 'disabled';

    const PAYOUT_RUN_DATE_NAME = 'payout_run_date';
    const PAYOUT_RUN_DATE_ID   = 6;

    CONST PAYOUT_MIN_AMOUNT_NAME = 'payout_min_amount';
    CONST PAYOUT_MIN_AMOUNT_ID   = 7;


    public function getJsonValue()
    {
        return json_decode($this->value);
    }
}
