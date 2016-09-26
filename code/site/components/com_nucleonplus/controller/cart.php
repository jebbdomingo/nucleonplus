<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */


/**
 * Cart Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComNucleonplusControllerCart extends ComKoowaControllerModel
{
    protected function _actionAdd(KControllerContextInterface $context)
    {
        $user       = $this->getObject('user');
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $data       = $context->request->data;
        $itemExists = false;

        $cart = $this->getModel()->account_id($account->id)->fetch();

        if (count($cart))
        {
            foreach ($cart as $item)
            {
                if ($item->package_id == $data->package_id)
                {
                    $item->quantity += $data->quantity;
                    $item->save();

                    $itemExists = true;
                }
            }
        }

        if (!$itemExists)
        {
            $data->account_id = $account->id;

            parent::_actionAdd($context);
        }


        $response = $context->getResponse();
        $response->addMessage('Item added to your shopping cart');

        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=cart', $identifier->package);

        $response->setRedirect(JRoute::_($url, false));
    }

    protected function _actionUpdatecart(KControllerContextInterface $context)
    {
        $user    = $this->getObject('user');
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $data    = $context->request->data;

        foreach ($data->quantity as $id => $qty)
        {
            $item = $this->getModel()->id($id)->fetch();
            $item->quantity = (int) $qty;
            $item->save();
        }

        $response = $context->getResponse();
        $response->addMessage('You shopping cart has been updated');
    }

    protected function _actionDeleteitem(KControllerContextInterface $context)
    {
        $user    = $this->getObject('user');
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $data    = $context->request->data;
        $id      = $data->item_id;

        $item = $this->getModel()->id($id)->fetch();
        $item->delete();

        $response = $context->getResponse();
        $response->addMessage('Item has deleted from your shopping cart', 'warning');
    }
}