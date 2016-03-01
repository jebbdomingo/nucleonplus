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
        $member = $this->_storeMember($this->_constructMember());

        if ($member->id) {
            return $this->_createAccount($member);
        }

        return false;
    }

    /**
     * Construct Joomla user
     *
     * @return object
     */
    protected function _constructMember()
    {
        $jUser               = new stdClass();
        $jUser->id           = $this->id;
        $jUser->name         = $this->name;
        $jUser->username     = $this->username;
        $jUser->password     = JUserHelper::genRandomPassword();
        $jUser->requireReset = 1;
        $jUser->email        = $this->email;
        $jUser->sendEmail    = 1;
        $jUser->sponsor_id   = $this->sponsor_id;
        //$jUser->activation   = JApplicationHelper::getHash($jUser->password);
        //$jUser->block        = 1;

        return $jUser;
    }

    /**
     * Store the article thru Joomla API
     *
     * @param object $jUser
     *
     * @return boolean
     */
    protected function _storeMember($jUser)
    {
        jimport( 'joomla.user.helper');

        $data = (array)$jUser;
        $user = new JUser;

        if(!$user->bind($data)) {
            throw new Exception("Could not bind data. Error: " . $user->getError());
        }

        if (!$user->save()) {
            throw new Exception("Could not save user. Error: " . $user->getError());
        }

        JUserHelper::addUserToGroup($user->id, self::_USER_GROUP_REGISTERED_);

        $this->id = $user->id;

        return $user;
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
        
        if ($entity->save())
        {
            $this->account_id = $entity->id;
            return true;
        }

        return false;
    }
}