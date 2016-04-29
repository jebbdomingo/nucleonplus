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
 * Employee Account Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
 */
class ComNucleonplusModelEntityEmployeeaccount extends KModelEntityRow
{
    /**
     * Prevent deletion of employee account
     * An employee account can only be deactivated or terminated but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Activate
     * Encapsulate activation business logic
     *
     * @return [type] [description]
     */
    public function activate()
    {
        if ($this->EmployeeRef == 0) {
            throw new Exception("Employee Activation Error: EmployeeRef is required");
        }

        $this->status = 'active';
    }
}