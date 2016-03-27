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
 * Account Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
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