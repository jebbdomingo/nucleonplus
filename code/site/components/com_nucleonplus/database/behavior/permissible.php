<?php
/**
 * @package     Nucleon+
 * @copyright   Copyright (C) 2016 - 2020 Nucleon + Co.
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusDatabaseBehaviorPermissible extends KDatabaseBehaviorAbstract
{
    /**
     * Check if a user can perform an action
     *
     * @param string $action
     *
     * @return boolean
     */
    public function canPerform($action)
    {
        $user      = $this->getObject('user');
        $component = 'com_' . $this->getTable()->getIdentifier()->package;

        return $user->isAuthentic() && (bool)$user->authorise('core.'.$action, $component);
    }
}