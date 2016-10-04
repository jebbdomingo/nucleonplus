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
                'awaiting_payment'      => array('label' => 'Awaiting Payment',      'type' => 'warning'),
                'awaiting_verification' => array('label' => 'Awaiting Verification', 'type' => 'warning'),
                'processing'            => array('label' => 'Processing',            'type' => 'info'),
                'shipped'               => array('label' => 'Shipped',               'type' => 'primary'),
                'delivered'             => array('label' => 'Delivered',             'type' => 'success'),
                'completed'             => array('label' => 'Completed',             'type' => 'success'),
                'cancelled'             => array('label' => 'Cancelled',             'type' => 'default'),
                'void'                  => array('label' => 'Void',                  'type' => 'default'),
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
