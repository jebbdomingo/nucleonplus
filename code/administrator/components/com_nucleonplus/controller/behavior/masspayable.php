<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerBehaviorMasspayable extends KControllerBehaviorAbstract
{
    protected function _afterProcessing(KControllerContextInterface $context)
    {
        $env       = getenv('APP_ENV');
        $entity    = $context->result;
        $config    = $this->getObject('com://admin/nucleonplus.model.configs')->item('dragonpay')->fetch();
        $dragonpay = $config->getJsonValue();

        $parameters = array(
            'apiKey'        => $dragonpay->payout_api_key,
            'merchantTxnId' => $entity->id,
            'userName'      => $entity->_account_bank_account_name,
            'amount'        => (float) $entity->amount,
            'currency'      => 'PHP',
            'description'   => "Payout for {$entity->name} amounting to {$entity->amount}",
            'procId'        => 'BDO',
            'procDetail'    => $entity->_account_bank_account_number,
            'runDate'       => gmdate('Y-m-d'),
            'email'         => $entity->email,
            'mobileNo'      => $entity->_account_mobile
        );

        var_dump($dragonpay->payout_url_test);
        var_dump($parameters);

        $client   = new SoapClient("{$dragonpay->payout_url_test}?wsdl");
        $resource = $client->RequestPayoutEx($parameters);

        var_dump($resource);

        $result   = $resource->RequestPayoutExResult;

        var_dump($result);

        die('testing');

        $entity->payout_service_result = $result;
        $entity->save();
    }
}
