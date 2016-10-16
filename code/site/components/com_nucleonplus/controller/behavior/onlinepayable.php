<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerBehaviorOnlinepayable extends KControllerBehaviorAbstract
{
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $env       = getenv('APP_ENV');
        $entity    = $context->result;
        $config    = $this->getObject('com://admin/nucleonplus.model.configs')->item('dragonpay')->fetch();
        $dragonpay = $config->getJsonValue();

        $parameters = array(
            'merchantid'  => $dragonpay->merchant_id,
            'txnid'       => $entity->id,
            'amount'      => number_format($entity->total, 2, '.', ''),
            'ccy'         => 'PHP',
            'description' => 'Order description.',
            'email'       => $this->getObject('user')->getEmail(),
        );

        $parameters['key'] = $dragonpay->password;
        $digest_string     = implode(':', $parameters);

        unset($parameters['key']);

        $parameters['digest'] = sha1($digest_string);
        $parameters['mode']   = $entity->payment_mode;

        $url = $env == 'production' ? "{$dragonpay->url_prod}?" : "{$dragonpay->url_test}?";
        $url .= http_build_query($parameters, '', '&');

        $context->response->setRedirect(JRoute::_($url, false));
    }
}
