<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerPayoutprocessor extends ComKoowaControllerModel
{
    protected function _actionUpdatepayoutstatus(KControllerContextInterface $context)
    {
        $data                        = $context->request->data;
        $data->id                    = $data->txnid;
        $data->payout_service_status = $data->status;
        $data->payout_service_msg    = $data->message;
        $result                      = 'result=OK';

        switch ($data->status) {
            case 'S':
                $data->status = ComNucleonplusModelEntityPayout::PAYOUT_STATUS_DISBURSED;
                break;
        }

        if ($data->status == 'P')
        {
            $data->payment_status = $data->status;
        }
        
        parent::_actionEdit($context);

        exit("{$result}");
    }
}
