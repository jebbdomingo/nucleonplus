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
        $user    = $this->getObject('user');
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $data    = $context->request->data;

        $cart      = $this->getModel()->account_id($account->id)->fetch();
        $cartItems = array();

        if (count($cart))
        {
            // Add item(s) to the cart
            if ($items = $cart->getItems())
            {
                foreach ($items as $item)
                {
                    $cartItems[] = $item->ItemRef;

                    // Existing item, update quantity instead
                    if ($item->ItemRef == $data->ItemRef)
                    {
                        $item->quantity += $data->quantity;
                        $item->save();
                    }
                }
            }

            if (!in_array($data->ItemRef, $cartItems))
            {
                // New item
                $cartItemData = array(
                    'cart_id'  => $cart->id,
                    'ItemRef'  => $data->ItemRef,
                    'quantity' => $data->quantity,
                );

                $item = $this->getObject('com://admin/nucleonplus.model.cartitems')->create($cartItemData);
                $item->save();
            }
        }
        else
        {
            // New cart
            $data->account_id = $account->id;
            $cart = parent::_actionAdd($context);

            // New item
            $cartItemData = array(
                'cart_id'  => $cart->id,
                'ItemRef'  => $data->ItemRef,
                'quantity' => $data->quantity,
            );
            $item = $this->getObject('com://admin/nucleonplus.model.cartitems')->create($cartItemData);
            $item->save();
        }

        $response = $context->getResponse();
        $response->addMessage('Item added to your shopping cart');

        $identifier = $context->getSubject()->getIdentifier();
        $itemid     = 119;
        $url        = sprintf('index.php?option=com_%s&view=cart&Itemid=%s', $identifier->package, $itemid);

        $response->setRedirect(JRoute::_($url, false));
    }

    protected function _actionUpdatecart(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $cart = $this->getModel()->fetch();
        } else {
            $cart = $context->result;
        }

        if (count($cart))
        {
            $cart->setProperties($context->request->data->toArray());
            $cart->save();

            if (in_array($cart->getStatus(), array(KDatabase::STATUS_FETCHED, KDatabase::STATUS_UPDATED)))
            {
                foreach ($cart->getItems() as $item)
                {
                    $item->quantity = (int) $context->request->data->quantity[$item->id];
                    $item->save();
                }

                $context->response->addMessage('You shopping cart has been updated');
            }
            else $context->response->addMessage($cart->getStatusMessage(), 'error');
        }
    }

    protected function _actionConfirm(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $cart = $this->getModel()->fetch();
        } else {
            $cart = $context->result;
        }

        if (count($cart))
        {
            $cart->setProperties($context->request->data->toArray());
            $cart->save();

            if (in_array($cart->getStatus(), array(KDatabase::STATUS_FETCHED, KDatabase::STATUS_UPDATED)))
            {
                foreach ($cart->getItems() as $item)
                {
                    $item->quantity = (int) $context->request->data->quantity[$item->id];
                    $item->save();
                }

                $url = 'index.php?option=com_nucleonplus&view=cart&layout=confirm';
                
            }
            else 
            {
                $itemid = 119;
                $context->response->addMessage($cart->getStatusMessage(), 'error');
                $url = 'index.php?option=com_nucleonplus&view=cart&Itemid=' . $itemid;
            }
        }

        $context->response->setRedirect(JRoute::_($url, false));
    }

    protected function _actionDeleteitem(KControllerContextInterface $context)
    {
        $user    = $this->getObject('user');
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $data    = $context->request->data;
        $id      = $data->item_id;

        $item = $this->getObject('com://admin/nucleonplus.model.cartitems')->id($id)->fetch();
        $item->delete();

        $response = $context->getResponse();
        $response->addMessage('Item has deleted from your shopping cart', 'warning');
    }
}