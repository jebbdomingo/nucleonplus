<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * 
 * @author Jebb Domingo <https://github.com/jebbdomingo>
 */
interface ComNucleonplusAccountingServiceMemberInterface
{
    /**
     * @param KModelEntityInterface $member
     * @param string                $action
     *
     * @return mixed
     */
    public function pushMember(KModelEntityInterface $member, $action);
}