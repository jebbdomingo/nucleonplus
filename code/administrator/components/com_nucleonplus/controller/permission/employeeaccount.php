<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerPermissionEmployeeaccount extends ComKoowaControllerPermissionAbstract
{
    public function canAdd()
    {
        $result = false;

        if (parent::canAdd() && JFactory::getUser()->get('isRoot')) {
            $result = true;
        }

        return $result;
    }
}
