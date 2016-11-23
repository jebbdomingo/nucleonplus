<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusDispatcherHttp extends ComKoowaDispatcherHttp
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller' => 'order'
        ));
        
        parent::_initialize($config);
    }

    protected function _actionDispatch(KDispatcherContextInterface $context)
    {
        $view        = $this->getRequest()->query->view;
        $excemptions = array('products', 'dragonpay', 'dragonpaypo');

        if ($view && (!in_array($view, $excemptions) && !$this->getUser()->isAuthentic()))
        {
            $message = 'Please login to access your account';
            $context->response->setRedirect($context->request->getBaseUrl()->toString(), $message, 'warning');
            $context->response->send();
        }
        else return parent::_actionDispatch($context);
    }

    public function getRequest()
    {
        $request = parent::getRequest();
        $query   = $request->query;
        $user    = $this->getObject('user');

        if ($query->view == 'account') {
            $query->id = (int) $user->getId();
        }
        else $query->account_id = (int) $user->getId();

        if ($query->view == 'member') {
            $query->id = (int) $user->getId();
        }

        // Manage cart
        if ($query->view == 'cart') {
            $query->id = (int) $this->_manageCart();
        }

        // Update payout status
        if ($query->view == 'dragonpay' && $query->api == 'payout' && $query->switch == 'postback' && $request->getMethod() == 'GET') {
            $this->_updatePayoutStatus($query);
        }

        // Verify online payment
        if ($query->view == 'dragonpay' && $query->api == 'payment' && $query->switch == 'postback') {
            $this->_verifyOnlinePayment($request->data);
        }

        // Show online payment status
        if ($query->view == 'dragonpay' && $query->api == 'payment' && $query->switch == 'returnurl') {
            $this->_showOnlinePaymentStatus($query);
        }

        return $request;
    }

    protected function _updatePayoutStatus($query)
    {
        $config    = $this->getObject('com://admin/nucleonplus.model.configs')->item('dragonpay')->fetch();
        $dragonpay = $config->getJsonValue();
        $result    = 'result=OK';

        if ($this->_login($dragonpay->nuc_user, $dragonpay->nuc_password))
        {
            try
            {
                $controller = $this->getObject('com://site/nucleonplus.controller.payoutprocessor');
                $controller->id($query->txnid);
                $controller->updatepayoutstatus($query->toArray());
            }
            catch (Exception $e)
            {
                // Transform error message to THIS_FORMAT
                $result = 'result=' . str_replace(' ', '_', strtoupper($e->getMessage()));
            }

            $this->_logout();
        }
        else $result = 'result=FAIL_AUTHENTICATION_ERROR';

        exit("{$result}");
    }

    protected function _verifyOnlinePayment($data)
    {
        $config    = $this->getObject('com://admin/nucleonplus.model.configs')->item('dragonpay')->fetch();
        $dragonpay = $config->getJsonValue();
        $result    = 'result=OK';

        if ($this->_login($dragonpay->nuc_user, $dragonpay->nuc_password))
        {
            try
            {
                $controller = $this->getObject('com://site/nucleonplus.controller.dragonpay');
                $controller->id($data->txnid);
                $controller->verifyonlinepayment($data->toArray());
            }
            catch (Exception $e)
            {
                // Transform error message to THIS_FORMAT
                $result = 'result=' . str_replace(' ', '_', strtoupper($e->getMessage()));
            }

            $this->_logout();
        }
        else $result = 'result=FAIL_AUTHENTICATION_ERROR';

        exit("{$result}");
    }

    protected function _showOnlinePaymentStatus($query)
    {
        $controller = $this->getObject('com://site/nucleonplus.controller.dragonpay');
        $controller->id($query->txnid);
        $controller->showstatus($query->toArray());
    }

    protected function _manageCart()
    {
        $model = $this->getObject('com://admin/nucleonplus.model.carts');
        $cart  = $model
            ->customer($this->getObject('user')->getId())
            ->interface(ComNucleonplusModelEntityCart::INTERFACT_SITE)
            ->fetch()
        ;

        if (count($cart))
        {
            $id = $cart->id;
        }
        else
        {
            $cart = $model->create(array(
                'customer'  => $this->getObject('user')->getId(),
                'interface' => 'site'
            ));
            $cart->save();

            $id = $cart->id;
        }

        return $id;
    }

    protected function _login($user, $password)
    {
        $loggedIn = (bool) $this->getObject('user')->getId();

        if (!$loggedIn)
        {
            $app = JFactory::getApplication('site');
            jimport('joomla.plugin.helper');

            $credentials = array(
                'username' => $user,
                'password' => $password
            );

            $app->login($credentials);

            // $data  = parent::getRequest()->data->toArray();
            // $query = parent::getRequest()->getUrl()->getQuery(true);
            // $query = array_merge($query, $data);

            // parent::getRequest()->getUrl()->setQuery($query);

            // $url = (string) parent::getRequest()->getUrl();

            // JFactory::getApplication()->redirect($url);
            
            $loggedIn = true;
        }

        return $loggedIn;
    }

    protected function _logout()
    {
        jimport('joomla.plugin.helper');
        $app = JFactory::getApplication('site');
        $app->logout();
    }
}