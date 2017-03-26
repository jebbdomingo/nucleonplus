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
            'controller' => 'account'
        ));
        
        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();
        $query   = $request->query;
        $user    = $this->getObject('user');

        if ($query->view == 'cart')
        {
            $model = $this->getObject('com://admin/nucleonplus.model.carts');
            $cart  = $model
                ->customer($query->customer)
                ->interface(ComNucleonplusModelEntityCart::INTERFACE_ADMIN)
                ->fetch()
            ;

            if (count($cart))
            {
                $id = $cart->id;
            }
            else
            {
                $cart = $model->create(array(
                    'customer'  => $query->customer,
                    'interface' => ComNucleonplusModelEntityCart::INTERFACE_ADMIN
                ));
                $cart->save();

                $id = $cart->id;
            }

            $query->id = (int) $id;
        }

        return $request;
    }
}
