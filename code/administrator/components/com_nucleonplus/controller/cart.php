<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerCart extends ComCartControllerCart
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.add', '_validateAdd');
        $this->addCommandCallback('before.checkout', '_validateCheckout');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'model' => 'com:nucleonplus.model.carts'
        ));

        parent::_initialize($config);
    }

    protected function _validateCheckout(KControllerContextInterface $context)
    {
        $translator = $this->getObject('translator');
        $result     = false;

        try
        {
            $cart = $this->getModel()->fetch();

            if (count($cart->getItems()) == 0) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please add an item to checkout'));
            }

            $result = true;
        }
        catch(Exception $e)
        {
            $context->response->setRedirect($context->request->getReferrer(), $e->getMessage(), 'error');
            $context->response->send();
        }

        return $result;
    }

    protected function _validateAdd(KControllerContextInterface $context)
    {
        $data       = $context->request->data;
        $translator = $this->getObject('translator');
        $result     = false;

        try
        {
            $cart = $this->getModel()->fetch();

            $quantity = (int) $data->form_quantity;

            if (empty($data->ItemRef) || !$quantity) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please select an item and specify quantity'));
            }

            $result = true;
        }
        catch(Exception $e)
        {
            $context->response->setRedirect($context->request->getReferrer(), $e->getMessage(), 'error');
            $context->response->send();
        }

        return $result;
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $data      = $context->request->data;
        $cart      = $this->getModel()->fetch();
        $cartItems = array();

        if (count($cart))
        {
            // Add item(s) to the cart
            if ($items = $cart->getItems())
            {
                foreach ($items as $item)
                {
                    $cartItems[] = $item->row;

                    // Existing item, update quantity instead
                    if ($item->row == $data->ItemRef)
                    {
                        $item->quantity += $data->form_quantity;
                        $item->save();
                    }
                }
            }

            if (!in_array($data->ItemRef, $cartItems))
            {
                // New item
                $cartItemData = array(
                    'cart_id'  => $cart->id,
                    'row'      => $data->ItemRef,
                    'quantity' => $data->form_quantity,
                );

                $item = $this->getObject('com://admin/nucleonplus.model.cartitems')->create($cartItemData);
                $item->save();
            }
        }

        $response = $context->getResponse();
        $response->addMessage('Item added to the shopping cart');

        return $cart;
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

    protected function _actionDeleteitem(KControllerContextInterface $context)
    {
        $data  = $context->request->data;
        $ids   = $data->id;
        $items = $this->getObject('com://admin/nucleonplus.model.cartitems')->id($ids)->fetch();

        foreach ($items as $item) {
            $item->delete();
        }

        $response = $context->getResponse();
        $response->addMessage('Item has deleted from your shopping cart', 'warning');
    }

    protected function _actionCheckout(KControllerContextInterface $context)
    {
        $data = array(
            'account_id' => $context->request->data->account_id,
            'cart_id'    => $context->request->data->cart_id,
        );

        return $this->getObject('com://admin/nucleonplus.controller.order')->add($data);
    }
}
