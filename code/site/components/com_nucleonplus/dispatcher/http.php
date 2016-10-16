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
        $excemptions = array('packages');

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
        } else $query->account_id = (int) $user->getId();

        if ($query->view == 'member') {
            $query->id = (int) $user->getId();
        }

        if ($query->view == 'cart')
        {
            $model = $this->getObject('com://admin/nucleonplus.model.carts');
            $cart  = $model->account_id($user->getId())->fetch();

            if (count($cart))
            {
                $id = $cart->id;
            }
            else
            {
                $cart = $model->create(array(
                    'account_id' => $user->getId()
                ));
                $cart->save();

                $id = $cart->id;
            }

            $query->id = (int) $id;
        }

        return $request;
    }
}