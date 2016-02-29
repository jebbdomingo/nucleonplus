<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewOrdersHtml extends KViewHtml
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $user    = $this->getObject('user');
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user->getId())->fetch();

        $input = JFactory::getApplication()->input;

        $status = $this->getUrl()->getQuery(true)['order_status'];

        $orders  = $this->getObject('com://admin/nucleonplus.model.orders')->account_id($account->id)->order_status($status)->fetch();

        $this->_data['memberOrders'] = $orders;
    }
}
