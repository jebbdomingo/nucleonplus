<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerPermissionOrder extends ComKoowaControllerPermissionAbstract
{
    /**
     * Specialized permission check
     * Checks if the user can confirm a payment for an order
     *
     * @return boolean
     */
    public function canConfirm()
    {
        $data    = $this->getObject('com://site/rewardlabs.accounting.data');

        if ($this->getModel()->fetch()->created_by <> $this->getObject('user')->getId())
        {
            return false;
        }
        elseif (!$data->CONFIG_ONLINE_PURCHASE_ENABLED)
        {
            return false;
        }
        else return parent::canSave();
    }

    /**
     * Specialized permission check
     * Checks if the user can add an order
     *
     * @return boolean
     */
    public function canAdd()
    {
        $data    = $this->getObject('com://site/rewardlabs.accounting.data');
        $user    = $this->getObject('user');
        $account = $this->getObject('com://site/nucleonplus.model.accounts')->id($user->getId())->fetch();

        if (in_array($account->status, array('new', 'pending', 'terminated'))) {
            return false;
        } elseif (!$data->CONFIG_ONLINE_PURCHASE_ENABLED) {
            return false;
        } elseif (JFactory::getUser()->id || $this->getObject('user')->isAuthentic()) {
            return true;
        } else {
            return parent::canAdd();
        }
    }
}