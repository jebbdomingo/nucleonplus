<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * Shippable Database Behavior
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Database\Behavior
 */
class ComNucleonplusDatabaseBehaviorShippable extends KDatabaseBehaviorAbstract
{
    /**
     * Get the user that ship the order
     *
     * @return KUserInterface|null Returns a User object or NULL if no user could be found
     */
    public function getShipper()
    {
        $provider = $this->getObject('user.provider');

        if ($this->hasProperty('shipped_by') && !empty($this->shipped_by))
        {
            if($this->_shipper_id && $this->_shipper_id == $this->shipped_by
                && !$provider->isLoaded($this->shipped_by))
            {
                $data = array(
                    'id'         => $this->_shipper_id,
                    'email'      => $this->_shipper_email,
                    'name'       => $this->_shipper_name,
                    'username'   => $this->_shipper_username,
                    'authentic'  => false,
                    'enabled'    => !$this->_shipper_block,
                    'expired'    => (bool) $this->_shipper_activation,
                    'attributes' => json_decode($this->_shipper_params)
                );

                $user = $provider->store($this->_shipper_id, $data);
            }
            else $user = $provider->load($this->shipped_by);
        }
        else $user = $provider->load(0);

        return $user;
    }

    /**
     * Set shipping information
     *
     * Requires a 'shipped_by' column
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        if (!$context->query->isCountQuery())
        {
            $context->query
                ->columns(array('_shipper_id'         => '_shipper.id'))
                ->columns(array('_shipper_name'       => '_shipper.name'))
                ->columns(array('_shipper_username'   => '_shipper.username'))
                ->columns(array('_shipper_email'      => '_shipper.email'))
                ->columns(array('_shipper_params'     => '_shipper.params'))
                ->columns(array('_shipper_block'      => '_shipper.block'))
                ->columns(array('_shipper_activation' => '_shipper.activation'))
                ->columns(array('shipped_by_name'    => '_shipper.name'))
                ->join(array('_shipper' => 'users'), 'tbl.shipped_by = _shipper.id');
        }
    }

    /**
     * Check if the behavior is supported
     *
     * Behavior requires a 'shipped_by' or 'shipped_on' row property
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $table = $this->getMixer();

        //Only check if we are connected with a table object, otherwise just return true.
        if ($table instanceof KDatabaseTableInterface)
        {
            if (!$table->hasColumn('shipped_by') && !$table->hasColumn('shipped_on'))  {
                return false;
            }
        }

        return true;
    }
}
