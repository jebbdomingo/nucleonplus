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
        if (!$this->getUser()->isAuthentic())
        {
            $response = $context->getResponse();
            $response->addMessage('Please login to access your account');

            $identifier = $context->getSubject()->getIdentifier();
            $url        = sprintf('index.php?option=com_%s', $identifier->package);

            $response->setRedirect(JRoute::_($url, false));
        }
        else return parent::_actionDispatch($context);
    }

    public function getRequest()
    {
        $request = parent::getRequest();
        $query   = $request->query;
        
        $user    = $this->getObject('user');
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user->getId())->fetch();

        if ($query->view == 'account') {
            $query->id = $account->id;
        }

        $query->account_id = $account->id;

        return $request;
    }
}