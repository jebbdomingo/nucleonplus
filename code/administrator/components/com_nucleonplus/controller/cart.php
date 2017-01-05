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
     * Sales Receipt Service
     *
     * @var ComNucleonplusAccountingServiceInventoryInterface
     */
    protected $_inventory_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.checkout', '_validateCheckout');
        $this->addCommandCallback('before.checkout', '_checkInventory');
        $this->addCommandCallback('after.updatecart', '_checkInventory');
        $this->addCommandCallback('after.add', '_checkInventory');

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

        // Reward service
        $this->_reward = $config->reward;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'model'             => 'com:nucleonplus.model.carts',
            'inventory_service' => 'com://admin/nucleonplus.accounting.service.inventory',
        ));

        parent::_initialize($config);
    }

    protected function _validateAdd(KControllerContextInterface $context)
    {
        $data           = $context->request->data;
        $data->row      = $data->ItemRef;
        $data->quantity = $data->form_quantity;

        var_dump($data->ItemRef);
        echo '<br />';
        var_dump($data->form_quantity);
        echo '<br />';
        var_dump($data->row);
        echo '<br />';
        var_dump($data->quantity);
        echo '<br />';
        die('test');

        parent::_validateAdd($context);
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
        $data           = $context->request->data;
        $data->row      = $data->ItemRef;
        $data->quantity = $data->form_quantity;

        $cart = parent::_actionAdd($context);

        $response = $context->getResponse();
        $response->addMessage('Item added to the shopping cart');

        return $cart;
    }

    protected function _actionCheckout(KControllerContextInterface $context)
    {
        $data = array(
            'account_id' => $context->request->data->account_id,
            'cart_id'    => $context->request->data->cart_id,
        );

        $controller = $this->getObject('com://admin/nucleonplus.controller.order');
        $controller->add($data);
        
        $result = $controller->getResponse()->getMessages();

        if (isset($result['success']))
        {
            foreach ($result['success'] as $message) {
                $context->response->addMessage($message);
            }
        }
    }

    protected function _actionDeleteitem(KControllerContextInterface $context)
    {
        parent::_actionDeleteitem($context);

        $context->response->addMessage('Item has been deleted from the shopping cart');
    }
}
