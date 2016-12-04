<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerPermissionOrder extends ComNucleonplusControllerPermissionAbstract
{
    /**
     * Specialized permission check
     *
     * @return boolean
     */
    public function canProcess()
    {
        $result = false;
        $order  = $this->getModel()->fetch();

        $verified = $order->order_status == ComNucleonplusModelEntityOrder::STATUS_VERIFIED;

        if (parent::canEdit() && $verified && !$order->processed_by) {
            $result = true;
        }

        return $result;
    }
}
