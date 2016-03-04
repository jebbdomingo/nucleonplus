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
            $this->account_id = $this->_createAccount($user);
        }
        else $this->account_id = $this->_getAccount($user->id)->id;

        return true;
    }

    /**
     * Create corresponding account for each member/user
     *
     * @param JUser $user
     *
     * @return boolean
     */
    protected function _createAccount(JUser $user)
    {
        $account = $this->getObject('com://admin/nucleonplus.model.accounts');

        $entity = $account->create(array(
            'user_id'    => $user->id,
            'sponsor_id' => $user->sponsor_id,
            'status'     => 'active',
        ));
        
        // @todo delete the user if account creation failed
        if ($entity->save())
        {
            return $entity->id;
        }
        else throw new Exception("Could not account for user. Error: " . $user->getError());

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