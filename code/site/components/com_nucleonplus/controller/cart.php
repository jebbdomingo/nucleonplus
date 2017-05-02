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
    /**
     * Inventory service
     *
     * @var ComNucleonplusAccountingInventoryQuantityInterface
     */
    protected $_inventory_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        @ini_set('max_execution_time', 300);

        parent::__construct($config);

        $this->addCommandCallback('before.add', '_validateAdd');
        $this->addCommandCallback('after.add', '_checkInventory');
        $this->addCommandCallback('before.confirm', '_validateConfirm');
        $this->addCommandCallback('after.confirm', '_checkInventory');

        // Inventory service
        $identifier = $this->getIdentifier($config->inventory_service);
        $service    = $this->getObject($identifier);
        if (!($service instanceof ComNucleonplusAccountingServiceInventoryInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceInventoryInterface"
            );
        }
        else $this->_inventory_service = $service;
    }
    
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'inventory_service' => 'com://admin/nucleonplus.accounting.service.inventory',
        ));

        parent::_initialize($config);
    }

    protected function _validateAdd(KControllerContextInterface $context)
    {
        $data       = $context->request->data;
        $data->row  = $data->ItemRef;
        $translator = $this->getObject('translator');
        $result     = false;

        try
        {
            $cart     = $this->getModel()->fetch();
            $quantity = (int) $data->quantity;

            if (empty($data->row) || !$quantity) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please select an item and specify its quantity'));
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

    protected function _validateConfirm(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $cart = $this->getModel()->fetch();
        } else {
            $cart = $context->result;
        }

        $translator = $this->getObject('translator');
        $result     = false;

        try
        {
            if (count($cart->getItems()) == 0) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please add an item to confirm'));
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

    protected function _checkInventory(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $cart = $this->getModel()->fetch();
        } else {
            $cart = $context->result;
        }

        $translator = $this->getObject('translator');
        $error      = false;

        if (count($cart))
        {
            $itemQty = $cart->getItemQuantities();

            foreach ($itemQty as $id => $qty)
            {
                $result = $this->_inventory_service->getQuantity($id, true);

                if ($result['available'] < $qty)
                {
                    $error  = "Insufficient stock of {$result['Name']}, only ({$result['available']}) item/s left in stock and you already have ({$qty}) in your shopping cart";
                    
                    if (JDEBUG)
                    {
                        $error .= '<pre>' . print_r($itemQty, true) . '</pre>';
                        $error .= '<pre>' . print_r($result, true) . '</pre>';
                    }
                }
            }
        }

        if ($error)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $translator->translate($error), 'error');
            $context->getResponse()->send();
        }
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $data      = $context->request->data;
        $data->row = $data->ItemRef;
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
                    'row'      => $data->row,
                    'quantity' => $data->quantity,
                );

                $item = $this->getObject('com:cart.model.items')->create($cartItemData);
                $item->save();
            }
        }

        $response = $context->getResponse();
        $response->addMessage('Item added to your shopping cart');

        // Redirect to shopping cart view
        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=cart', $identifier->package);

        $response->setRedirect(JRoute::_($url, false));
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

                $url = 'index.php?option=com_nucleonplus&view=cart&layout=confirm&tmpl=koowa';
            }
            else 
            {
                $context->response->addMessage($cart->getStatusMessage(), 'error');
                $url = 'index.php?option=com_nucleonplus&view=cart';
            }
        }

        $context->response->setRedirect(JRoute::_($url, false));
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
        $data = $context->request->data;
        $items = $this->getObject('com:cart.model.items')->id($data->item_id)->fetch();

        foreach ($items as $item) {
            $item->delete();
        }

        $context->response->addMessage('Item has been deleted from your shopping cart', 'warning');
    }
}