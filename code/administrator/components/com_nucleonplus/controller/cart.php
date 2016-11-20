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

        return $this->getObject('com://admin/nucleonplus.controller.order')->add($data);
    }

    protected function _actionDeleteitem(KControllerContextInterface $context)
    {
        parent::_actionDeleteitem($context);

        $context->response->addMessage('Item has been deleted from your shopping cart');
    }
}
