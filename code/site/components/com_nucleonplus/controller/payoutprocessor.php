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
        $data     = $context->request->data;
        $data->id = $data->txnid;

        switch ($data->status) {
            case 'S':
                $data->status = ComNucleonplusModelEntityPayout::PAYOUT_STATUS_DISBURSED;
                break;

            case 'P':
                $data->payment_status = $data->status;
                break;
        }

        // Record dragonpay payout status
        $this->_recordPayoutStatus($data);

        return parent::_actionEdit($context);
    }

    protected function _recordPayoutStatus($data)
    {
        $controller = $this->getObject('com:dragonpay.controller.payout');
        $payout     = $this->getObject('com:dragonpay.model.payouts')->id($data->txnid)->fetch();

        if (count($payout) == 1)
        {
            $controller
                ->id($data->txnid)
                ->edit($data->toArray())
            ;
        }
    }
}
