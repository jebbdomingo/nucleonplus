<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityEmployee extends KModelEntityRow
{
    const _USER_GROUP_MANAGER_ = 6;

    /**
     * @var ComNucleonplusAccountingServiceEmployeeInterface
     */
    protected $_employee_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $identifier = $this->getIdentifier($config->employee_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceEmployeeInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceEmployeeInterface"
            );
        }
        else $this->_employee_service = $service;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'employee_service' => 'com:nucleonplus.accounting.service.employee'
        ));

        parent::_initialize($config);
    }

    /**
     * Saves the entity to the data store
     *
     * @return boolean
     */
    public function save()
    {
        jimport( 'joomla.user.helper');

        $employee = new KObjectConfig($this->getProperties());

        if ($this->isNew())
        {
            // Merge the following fields as these are not automatically updated by Nooku
            $employee->merge([
                'password'     => JUserHelper::genRandomPassword(),
                'requireReset' => 1,
                'sendEmail'    => 1,
                'name'         => "{$this->given_name} {$this->family_name}",
            ]);

            $user = new JUser;

            $data = $employee->toArray();
            if(!$user->bind($data)) {
                throw new Exception("Could not bind data. Error: " . $user->getError());
            }

            if ($user->save() === false) {
                throw new Exception("Could not save user. Error: " . $user->getError());
            }

            JUserHelper::addUserToGroup($user->id, self::_USER_GROUP_MANAGER_);
            $this->id          = $user->id;
            $employee          = $this->_createAccount($user->id);
            $this->employee_id = $employee->id;
        }
        else
        {
            $user = new JUser($employee->id);

            $employee->remove('password');

            $employee->merge([
                'name' => "{$this->given_name} {$this->family_name}",
            ]);
            $data = $employee->toArray();

            if(!$user->bind($data)) {
                throw new Exception("Could not bind data. Error: " . $user->getError());
            }

            if ($user->save() === false) {
                throw new Exception("Could not save user. Error: " . $user->getError());
            }

            $employee          = $this->_updateAccount($user->id);
            $this->employee_id = $employee->id;

            // Only push an update to a synced employee to accounting system
            if ($employee->EmployeeRef) {
                $this->_employee_service->pushEmployee($employee, 'update');
            }
        }

        return true;
    }

    /**
     * Create corresponding employee record for each user
     *
     * @param integer $userId
     *
     * @return KModelEntityInterface
     */
    protected function _createAccount($userId)
    {
        $model = $this->getObject('com://admin/nucleonplus.model.employeeaccounts');

        $account = $model->create(array(
            'id'                  => $userId,
            'user_id'             => $userId,
            'status'              => 'pending',
            'given_name'          => $this->given_name,
            'family_name'         => $this->family_name,
            'PrintOnCheckName'    => $this->name,
            'DepartmentRef'       => $this->DepartmentRef,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_name'   => $this->bank_account_name,
            'bank_account_type'   => $this->bank_account_type,
            'bank_account_branch' => $this->bank_account_branch,
            'phone'               => $this->phone,
            'mobile'              => $this->mobile,
            'street'              => $this->street,
            'city'                => $this->city,
            'state'               => $this->state,
            'postal_code'         => $this->postal_code,
        ));
        
        $account->save();
        $account = $model->id($account->id)->fetch();

        return $account;
    }

    /**
     * Update Employee record
     *
     * @param integer $userId
     *
     * @return KModelEntityInterface
     */
    protected function _updateAccount($userId)
    {
        $account = $this->getObject('com://admin/nucleonplus.model.employeeaccounts')->user_id($userId)->fetch();
        $account->given_name          = $this->given_name;
        $account->family_name         = $this->family_name;
        $account->PrintOnCheckName    = $this->name;
        $account->DepartmentRef       = $this->DepartmentRef;
        $account->bank_account_number = $this->bank_account_number;
        $account->bank_account_name   = $this->bank_account_name;
        $account->bank_account_type   = $this->bank_account_type;
        $account->bank_account_branch = $this->bank_account_branch;
        $account->phone               = $this->phone;
        $account->mobile              = $this->mobile;
        $account->street              = $this->street;
        $account->city                = $this->city;
        $account->state               = $this->state;
        $account->postal_code         = $this->postal_code;
        $account->save();

        return $account;
    }
}