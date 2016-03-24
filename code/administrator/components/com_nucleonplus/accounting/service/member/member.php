<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * @todo Implement a local queue of accounting/inventory transactions in case of trouble connecting to accounting system
 */
class ComNucleonplusAccountingServiceMember extends KObject implements ComNucleonplusAccountingServiceMemberInterface
{
    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_customer_controller;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_customer_controller = $this->getObject($config->customer_controller);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'customer_controller' => 'com:qbsync.controller.customer',
        ));

        parent::_initialize($config);
    }

    /**
     *
     * @param KModelEntityInterface $account
     *
     * @return KModelEntityInterface
     */
    public function createMember(KModelEntityInterface $account)
    {
        $data = array(
            'account_id'       => $account->id,
            'DisplayName'      => $account->_name,
            'PrimaryPhone'     => $account->phone,
            'Mobile'           => $account->mobile,
            'PrimaryEmailAddr' => $account->_email,
            'PrintOnCheckName' => $account->_name,
            'Line1'            => $account->street,
            'City'             => $account->city,
            'PostalCode'       => $account->postal_code,
            'Country'          => 'Philippines',
        );

        return $this->_customer_controller->add($data);
    }
}