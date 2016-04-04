<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelEntityOrder extends KModelEntityRow
{
    /**
     * Prevent deletion of order
     * An order can only be void but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Save action
     *
     * @return boolean
     */
    public function save()
    {
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($this->account_id)->fetch();

        switch ($account->status)
        {
            case 'new':
            case 'pending':
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account is currently inactive'));
                return false;
                break;

            case 'terminated':
                $this->setStatusMessage($this->getObject('translator')->translate('Unable to place order, the account was terminated'));
                return false;
                break;
            
            default:
                return parent::save();
                break;
        }
    }

    /**
     * Get the package items of this order
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getObject('com:nucleonplus.model.packageitems')->package_id($this->package_id)->fetch();
    }

    /**
     * Get the package details
     *
     * @return array
     */
    public function getPackage()
    {
        return $this->getObject('com:nucleonplus.model.packages')->id($this->package_id)->fetch();
    }

    /**
     * Get the reward details
     *
     * @return array
     */
    public function getReward()
    {
        return $this->getObject('com:nucleonplus.model.rewards')->product_id($this->id)->fetch();
    }
}