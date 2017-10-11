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
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.updatepayoutstatus', '_validatePayout');
    }

    protected function _validatePayout(KControllerContextInterface $context)
    {
        $data   = $context->request->data;
        $payout = $this->getObject('com://admin/nucleonplus.model.payouts')->id($data->txnid)->fetch();

        // Validate payout
        if (!count($payout)) {
            throw new Exception('INVALID_TRANSACTION');
        }

        // Validate digest from dragonpay
        $config     = $this->getObject('com://site/rewardlabs.model.configs')->item('dragonpay')->fetch();
        $dragonpay  = $config->getJsonValue();
        $parameters = array(
            'txnid'    => $data->txnid,
            'refno'    => $data->refno,
            'status'   => $data->status,
            'message'  => $data->message,
            'password' => $dragonpay->password
        );
        $digestStr = implode(':', $parameters);
        $digest    = sha1($digestStr);

        if ($data->digest !== $digest)
        {
            if (getenv('HTTP_APP_ENV') != 'production') {
                var_dump($digest);
            }

            throw new KControllerExceptionRequestInvalid('FAIL_DIGEST_MISMATCH');
        }
    }

    protected function _actionUpdatepayoutstatus(KControllerContextInterface $context)
    {
        $data = $context->request->data;

        // Record dragonpay payout status
        $this->_recordPayoutStatus($data);

        switch ($data->status) {
            case ComDragonpayModelEntityPayout::STATUS_SUCCESSFUL:
                $data->id = $data->txnid;

                $payout = $this->getObject('com://admin/nucleonplus.model.payouts')->id($data->id)->fetch();
                $payout->status = ComNucleonplusModelEntityPayout::PAYOUT_STATUS_DISBURSED;
                $payout->save();
                
                $this->_sendMail($payout);

                return $payout;

                break;
        }
    }

    protected function _recordPayoutStatus($data)
    {
        $controller = $this->getObject('com:dragonpay.controller.payout');
        $payout     = $controller->getModel()->id($data->txnid)->fetch();

        if (count($payout) == 1)
        {
            $controller
                ->id($data->txnid)
                ->edit($data->toArray())
            ;
        }
    }

    protected function _sendMail($payout)
    {
        // Send email notification
        $emailSubject = JText::sprintf('COM_NUCLEONPLUS_PAYOUT_EMAIL_FUNDS_TRANSFER_SUCCESSFUL_SUBJECT', $payout->id);
        $emailBody    = JText::sprintf(
            'COM_NUCLEONPLUS_PAYOUT_EMAIL_FUNDS_TRANSFER_SUCCESSFUL_BODY',
            $payout->name,
            'PHP 15.00',
            JUri::root()
        );

        $config = JFactory::getConfig();
        $mail   = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $payout->email, $emailSubject, $emailBody);

        // Check for an error.
        if ($mail !== true) {
            $context->response->addMessage(JText::_('COM_NUCLEONPLUS_PAYOUT_EMAIL_SEND_MAIL_FAILED'), 'error');
        }
    }
}
