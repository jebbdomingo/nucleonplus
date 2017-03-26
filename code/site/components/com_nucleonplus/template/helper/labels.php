<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusTemplateHelperLabels extends KTemplateHelperAbstract
{
    /**
     * Order status
     *
     * @param array $config
     *
     * @return string
     */
    public function orderStatus(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config
        ->append(array(
            'states'  => array(
                ComNucleonplusModelEntityOrder::STATUS_PENDING    => array('label' => 'Pending',          'type' => 'default'),
                ComNucleonplusModelEntityOrder::STATUS_PAYMENT    => array('label' => 'Awaiting Payment', 'type' => 'warning'),
                ComNucleonplusModelEntityOrder::STATUS_VERIFIED   => array('label' => 'Verified',         'type' => 'success'),
                ComNucleonplusModelEntityOrder::STATUS_PROCESSING => array('label' => 'Processing',       'type' => 'info'),
                ComNucleonplusModelEntityOrder::STATUS_SHIPPED    => array('label' => 'Shipped',          'type' => 'primary'),
                ComNucleonplusModelEntityOrder::STATUS_DELIVERED  => array('label' => 'Delivered',        'type' => 'success'),
                ComNucleonplusModelEntityOrder::STATUS_COMPLETED  => array('label' => 'Completed',        'type' => 'success'),
                ComNucleonplusModelEntityOrder::STATUS_CANCELLED  => array('label' => 'Cancelled',        'type' => 'default'),
            )
        ))
        ->append(array(
            'type'  => 'default',
            'value' => null
        ));

        $status = $config->states[$config->value];
        $html   = '<span class="label label-' . $status['type'] . '">' . $status['label'] . '</span>';

        return $html;
    }
}
