<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerPermissionAccount extends ComKoowaControllerPermissionAbstract
{
    /**
     * Specialized permission check
     * Checks if the user can close a ticket
     *
     * @return boolean
     */
    public function canClose()
    {
        if ($this->getModel()->fetch()->created_by === $this->getObject('user')->getId()) {
            return true;
        } else {
            return parent::canSave();
        }
    }
}