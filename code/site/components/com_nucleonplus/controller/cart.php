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
class ComNucleonplusControllerCart extends ComCartControllerCart
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);
    }
    
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'model' => 'com:nucleonplus.model.carts'
        ));

        parent::_initialize($config);
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $cart = parent::_actionAdd($context);

        $response = $context->getResponse();
        $response->addMessage('Item added to your shopping cart');

        // Redirect to shopping cart view
        $identifier = $context->getSubject()->getIdentifier();
        $itemid     = 119;
        $url        = sprintf('index.php?option=com_%s&view=cart&Itemid=%s', $identifier->package, $itemid);

        $response->setRedirect(JRoute::_($url, false));
    }

    // protected function _actionUpdatecart(KControllerContextInterface $context)
    // {
    //     if (!$context->result instanceof KModelEntityInterface) {
    //         $cart = $this->getModel()->fetch();
    //     } else {
    //         $cart = $context->result;
    //     }

    //     if (count($cart))
    //     {
    //         $cart->setProperties($context->request->data->toArray());
    //         $cart->save();

    //         if (in_array($cart->getStatus(), array(KDatabase::STATUS_FETCHED, KDatabase::STATUS_UPDATED)))
    //         {
    //             foreach ($cart->getItems() as $item)
    //             {
    //                 $item->quantity = (int) $context->request->data->quantity[$item->id];
    //                 $item->save();
    //             }

    //             $context->response->addMessage('You shopping cart has been updated');
    //         }
    //         else $context->response->addMessage($cart->getStatusMessage(), 'error');
    //     }
    // }

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
                $context->response->addMessage($cart->getStatusMessage(), 'error');
                $url = 'index.php?option=com_nucleonplus&view=cart';
            }
        }

        $itemid = 119;
        $url    .= "&Itemid={$itemid}";

        $context->response->setRedirect(JRoute::_($url, false));
    }

    protected function _actionDeleteitem(KControllerContextInterface $context)
    {
        $data     = $context->request->data;
        $data->id = $data->item_id;

        parent::_actionDeleteitem($context);

        $context->response->addMessage('Item has been deleted from your shopping cart');
    }
}