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
            // Add item(s) to the cart
            if ($items = $cart->getItems())
            {
                foreach ($items as $item)
                {
                    // Existing item, update quantity instead
                    if ($item->package_id == $data->package_id)
                    {
                        $item->quantity += $data->quantity;
                        $item->save();

                        $itemExists = true;
                    }
                    else
                    {
                        // New item
                        $cartItemData = array(
                            'cart_id'    => $cart->id,
                            'package_id' => $data->package_id,
                            'quantity'   => $data->quantity,
                        );

                        $item = $this->getObject('com://admin/nucleonplus.model.cartitems')->create($cartItemData);
                        $item->save();
                    }
                }
            }
        }
        else
        {
            // New cart
            $data->account_id = $account->id;
            $cart = parent::_actionAdd($context);

            // New item
            $cartItemData = array(
                'cart_id'    => $cart->id,
                'package_id' => $data->package_id,
                'quantity'   => $data->quantity,
            );
            $item = $this->getObject('com://admin/nucleonplus.model.cartitems')->create($cartItemData);
            $item->save();
        }

        $response = $context->getResponse();
        $response->addMessage('Item added to your shopping cart');

        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=cart', $identifier->package);

        $response->setRedirect(JRoute::_($url, false));
    }

    protected function _actionUpdatecart(KControllerContextInterface $context)
    {
        $data = $context->request->data;

        $cart = $this->getObject('com://admin/nucleonplus.model.carts')->id($data->cart_id)->fetch();
        $cart->address         = $data->address;
        $cart->city            = $data->city;
        $cart->state_province  = $data->state_province;
        $cart->region          = $data->region;

        $paymentChannel        = explode('|', $data->payment_channel);
        $cart->payment_channel = $paymentChannel[0];
        $cart->payment_type    = $paymentChannel[1];
        $cart->save();

        foreach ($cart->getItems() as $item)
        {
            $item->quantity = (int) $data->quantity[$item->id];
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

        $item = $this->getObject('com://admin/nucleonplus.model.cartitems')->id($id)->fetch();
        $item->delete();

        $response = $context->getResponse();
        $response->addMessage('Item has deleted from your shopping cart', 'warning');
    }
}