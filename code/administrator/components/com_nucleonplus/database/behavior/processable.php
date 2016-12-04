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
 * Processable Database Behavior
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Database\Behavior
 */
class ComNucleonplusDatabaseBehaviorProcessable extends KDatabaseBehaviorAbstract
{
    /**
     * Get the user that processed the resource
     *
     * @return KUserInterface|null Returns a User object or NULL if no user could be found
     */
    public function getAssignee()
    {
        $provider = $this->getObject('user.provider');

        if ($this->hasProperty('processed_by') && !empty($this->processed_by))
        {
            if($this->_assignee_id && $this->_assignee_id == $this->processed_by
                && !$provider->isLoaded($this->processed_by))
            {
                $data = array(
                    'id'         => $this->_assignee_id,
                    'email'      => $this->_assignee_email,
                    'name'       => $this->_assignee_name,
                    'username'   => $this->_assignee_username,
                    'authentic'  => false,
                    'enabled'    => !$this->_assignee_block,
                    'expired'    => (bool) $this->_assignee_activation,
                    'attributes' => json_decode($this->_assignee_params)
                );

                $user = $provider->store($this->_assignee_id, $data);
            }
            else $user = $provider->load($this->processed_by);
        }
        else $user = $provider->load(0);

        return $user;
    }

    /**
     * Set processed information
     *
     * Requires a 'processed_by' column
     *
     * @param KDatabaseContext  $context A database context object
     * @return void
     */
    protected function _beforeSelect(KDatabaseContext $context)
    {
        if (!$context->query->isCountQuery())
        {
            $context->query
                ->columns(array('_assignee_id'         => '_assignee.id'))
                ->columns(array('_assignee_name'       => '_assignee.name'))
                ->columns(array('_assignee_username'   => '_assignee.username'))
                ->columns(array('_assignee_email'      => '_assignee.email'))
                ->columns(array('_assignee_params'     => '_assignee.params'))
                ->columns(array('_assignee_block'      => '_assignee.block'))
                ->columns(array('_assignee_activation' => '_assignee.activation'))
                ->columns(array('processed_by_name'    => '_assignee.name'))
                ->join(array('_assignee' => 'users'), 'tbl.processed_by = _assignee.id');
        }
    }

    /**
     * Check if the behavior is supported
     *
     * Behavior requires a 'processed_by' or 'processed_on' row property
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $table = $this->getMixer();

        //Only check if we are connected with a table object, otherwise just return true.
        if ($table instanceof KDatabaseTableInterface)
        {
            if (!$table->hasColumn('processed_by') && !$table->hasColumn('processed_on'))  {
                return false;
            }
        }

        return true;
    }
}
