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
    protected function _actionVerifyonlinepayment(KControllerContextInterface $context)
    {
        $data                        = $context->request->data;
        $data->id                    = $data->txnid;
        $data->payout_service_status = $data->status;
        $data->payout_service_msg    = $data->message;
        $result                      = 'result=OK';

        if ($this->_login())
        {

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

            $this->_logout();
        }

        exit("{$result}");
    }

    protected function _login()
    {
        $config    = $this->getObject('com://admin/nucleonplus.model.configs')->item('dragonpay')->fetch();
        $dragonpay = $config->getJsonValue();

        $app = JFactory::getApplication('site');
        jimport('joomla.plugin.helper');

        $credentials = array(
            'username' => $dragonpay->nuc_user,
            'password' => $dragonpay->nuc_password
        );

        return $app->login($credentials);
    }

    protected function _logout()
    {
        jimport('joomla.plugin.helper');
        $app = JFactory::getApplication('site');
        $app->logout();
    }
}