<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusTemplateHelperOrdertimeline extends KTemplateHelperAbstract
{
    /**
     * Order status
     *
     * @param array $config
     *
     * @return string
     */
    public function timeline(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config
        ->append(array(
            'states'  => array(
                ComNucleonplusModelEntityOrder::STATUS_PAYMENT    => array('label' => 'Awaiting Payment', 'icon' => 'time'),
                ComNucleonplusModelEntityOrder::STATUS_VERIFIED   => array('label' => 'Verified', 'icon' => 'ok'),
                ComNucleonplusModelEntityOrder::STATUS_PROCESSING => array('label' => 'Processing', 'icon' => 'list-alt'),
                ComNucleonplusModelEntityOrder::STATUS_SHIPPED    => array('label' => 'Shipped', 'icon' => 'plane'),
            )
        ))
        ->append(array(
            'state' => null
        ));

        $html = null;

        if (array_key_exists($config->state, KObjectConfig::unbox($config->states)))
        {
            $html = '<div class="stepwizard">
                <div class="stepwizard-row">';

            foreach ($config->states as $state => $option)
            {
                $active = $state == $config->state ? 'btn-primary' : 'btn-default';

                $html .= '<div class="stepwizard-step">
                            <button type="button" class="btn ' . $active . ' btn-circle"><span class="glyphicon glyphicon-' . $option['icon'] . '" aria-hidden="true"></span></button>
                            <p>' . $option['label'] . '</p>
                        </div>';
            }

            $html .= '</div>
                </div>';
        }

        return $html;
    }
}
