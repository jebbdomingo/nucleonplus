<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityMember extends KModelEntityRow
{
    const _USER_GROUP_REGISTERED_ = 2;

    /**
     * @var ComNucleonplusAccountingServiceMemberInterface
     */
    protected $_member_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $identifier = $this->getIdentifier($config->member_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceMemberInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceMemberInterface"
            );
        }
        else $this->_member_service = $service;
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
            'member_service' => 'com:nucleonplus.accounting.service.member'
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

        $member = new KObjectConfig($this->getProperties());

        if ($this->isNew())
        {
            $user = new JUser;

            // Merge the following fields as these are not automatically updated by Nooku
            $member->merge([
                'password'     => JUserHelper::genRandomPassword(),
                'requireReset' => 1,
                'sendEmail'    => 1,
            ]);

            $data = $member->toArray();
            if(!$user->bind($data)) {
                throw new Exception("Could not bind data. Error: " . $user->getError());
            }

            if (!$user->save()) {
                throw new Exception("Could not save user. Error: " . $user->getError());
            }

            JUserHelper::addUserToGroup($user->id, self::_USER_GROUP_REGISTERED_);
            $this->id         = $user->id;
            $account          = $this->_createAccount($user->id);
            $this->account_id = $account->id;
        }
        else
        {
            $user = new JUser($member->id);

            $member->remove('password');
            $data = $member->toArray();

            if(!$user->bind($data)) {
                throw new Exception("Could not bind data. Error: " . $user->getError());
            }

            if (!$user->save(true)) {
                throw new Exception("Could not save user. Error: " . $user->getError());
            }

            $account          = $this->_updateAccount($user->id);
            $this->account_id = $account->id;

            // Only push an update to a synced member/customer to accounting system
            if ($account->CustomerRef) {
                $this->_member_service->pushMember($account, 'update');
            }
        }

        return true;
    }

    /**
     * Create corresponding account for each member/user
     *
     * @param integer $userId
     *
     * @return KModelEntityInterface|boolean
     */
    protected function _createAccount($userId)
    {
        $model = $this->getObject('com://admin/nucleonplus.model.accounts');

        $account = $model->create(array(
            'id'                  => $userId,
            'user_id'             => $userId,
            'sponsor_id'          => $this->sponsor_id,
            'PrintOnCheckName'    => $this->PrintOnCheckName,
            'status'              => 'pending',
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
     * Update Account
     *
     * @param integer $userId
     *
     * @return KModelEntityInterface
     */
    protected function _updateAccount($userId)
    {
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($userId)->fetch();

        $account->sponsor_id          = $this->sponsor_id;
        $account->PrintOnCheckName    = $this->PrintOnCheckName;
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

    /**
     * Get member account
     *
     * @param integer $user_id
     *
     * @return KModelEntityInterface
     */
    protected function _getAccount($user_id)
    {
        return $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user_id)->fetch();
    }
}