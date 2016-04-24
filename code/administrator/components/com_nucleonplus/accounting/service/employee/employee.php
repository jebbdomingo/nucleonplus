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

class ComNucleonplusAccountingServiceEmployee extends KObject implements ComNucleonplusAccountingServiceEmployeeInterface
{
    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_employee_controller;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_employee_controller = $this->getObject($config->employee_controller);
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
            'employee_controller' => 'com:qbsync.controller.employee',
        ));

        parent::_initialize($config);
    }

    /**
     *
     * @param KModelEntityInterface $employee
     * @param string                $action
     *
     * @return void
     */
    public function pushEmployee(KModelEntityInterface $employee, $action = 'add')
    {
        $data = array(
            'EmployeeRef'      => $employee->EmployeeRef,
            'employee_id'      => $employee->id,
            'GivenName'        => $employee->given_name,
            'FamilyName'       => $employee->family_name,
            'PrimaryPhone'     => $employee->phone,
            'Mobile'           => $employee->mobile,
            'PrimaryEmailAddr' => $employee->_user_email,
            'PrintOnCheckName' => $employee->_user_name,
            'Line1'            => $employee->street,
            'City'             => $employee->city,
            'State'            => $employee->state,
            'PostalCode'       => $employee->postal_code,
            'Country'          => 'Philippines',
            'action'           => $action,
        );

        return $this->_employee_controller->add($data);
    }
}