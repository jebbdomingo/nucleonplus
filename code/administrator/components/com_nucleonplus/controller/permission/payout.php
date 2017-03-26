<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerPermissionPayout extends ComKoowaControllerPermissionAbstract
{
    /**
     * Specialized permission check
     *
     * @return boolean
     */
    public function canProcessing()
    {
        $result = false;
        $config = $this->getObject('com://admin/nucleonplus.model.configs')
            ->item(ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_NAME)
            ->fetch()
        ;

        if ((JFactory::getUser()->id || $this->getObject('user')->isAuthentic()) && $config->value)
        {
            $result = true;
        }

        return $result;
    }
}
