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
    /**
     * Saves the entity to the data store
     *
     * @return boolean
     */
    public function save()
    {
        $member = $this->_constructMember();

        return ($this->_storeMember($member)) ? true : false;
    }

    /**
     * Construct Joomla user
     *
     * @return object
     */
    protected function _constructMember()
    {
        $jUser           = new stdClass();
        $jUser->id       = $this->id;
        $jUser->name     = $this->name;
        $jUser->username = $this->username;
        $jUser->email    = $this->email;

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

        return $user->id ;
    }
}