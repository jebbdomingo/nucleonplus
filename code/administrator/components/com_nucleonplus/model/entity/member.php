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
     * Saves the entity to the data store
     *
     * @return boolean
     */
    public function save()
    {
        jimport( 'joomla.user.helper');

        $member = new KObjectConfig($this->getProperties());

        // Merge the following fields as these are not automatically updated by Nooku
        $member->merge([
            'password'     => JUserHelper::genRandomPassword(),
            'requireReset' => 1,
            'sendEmail'    => 1,
            // 'activation' => JApplicationHelper::getHash($this->password);
            // 'block'      => 1;
        ]);

        $user = new JUser;

        if(!$user->bind($member->toArray())) {
            throw new Exception("Could not bind data. Error: " . $user->getError());
        }

        if (!$user->save()) {
            throw new Exception("Could not save user. Error: " . $user->getError());
        }

        if ($this->isNew())
        {
            JUserHelper::addUserToGroup($user->id, self::_USER_GROUP_REGISTERED_);
            $this->id         = $user->id;
            $this->account_id = $this->_createAccount($user->id, $user->sponsor_id)->id;
        }
        else $this->account_id = $this->_updateAccount($user->id)->id;

        return true;
    }

    /**
     * Create corresponding account for each member/user
     *
     * @param integer $userId
     * @param integer $sponsorId
     *
     * @return boolean
     */
    protected function _createAccount($userId, $sponsorId)
    {
        $model = $this->getObject('com://admin/nucleonplus.model.accounts');

        $account = $model->create(array(
            'user_id'             => $userId,
            'sponsor_id'          => $sponsorId,
            'status'              => 'active',
            'bank_account_number' => $this->bank_account_number,
            'bank_account_name'   => $this->bank_account_name,
            'bank_account_type'   => $this->bank_account_type,
            'bank_account_branch' => $this->bank_account_branch,
            'street'              => $this->street,
            'city'                => $this->city,
            'state'               => $this->state,
            'postal_code'         => $this->postal_code,
        ));
        
        // TODO delete the user if account creation failed
        if ($account->save()) {
            return $account;
        }
        else throw new Exception('Could not create account for user');

        return false;
    }

    /**
     * Update Account
     *
     * @param integer $userId
     *
     * @return KModelEntityInterface|boolean
     */
    protected function _updateAccount($userId)
    {
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($userId)->fetch();

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
        
        if ($account->save()) {
            return $account;
        }

        return false;
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