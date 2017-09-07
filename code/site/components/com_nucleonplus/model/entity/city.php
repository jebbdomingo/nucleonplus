<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComNucleonplusModelEntityCity extends KModelEntityRow
{
    const DESTINATION_METRO_MANILA = 47;

    public function getPropertyCity()
    {
        return $this->_name;
    }
}