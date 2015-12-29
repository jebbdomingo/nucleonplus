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
 * Status Template Helper
 *
  * @package Nucleon Plus
 */
class ComNucleonplusTemplateHelperListbox extends ComKoowaTemplateHelperListbox
{
    protected function _initialize(KObjectConfig $config)
    {
        $config
        ->append(array(
            'status' => array(
                array('label' => 'New', 'value' => 'new'),
                array('label' => 'Open', 'value' => 'open'),
                array('label' => 'Pending', 'value' => 'pending'),
                array('label' => 'Solved', 'value' => 'solved'),
                array('label' => 'Closed', 'value' => 'closed')
            )
        ))
        ->append(array(
            'statusFilters' => array(
                'all'     => 'All',
                'new'     => 'New',
                'open'    => 'Open',
                'pending' => 'Pending',
                'solved'  => 'Solved',
                'closed'  => 'Closed'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Generates status list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function optionList($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'status',
            'selected' => null,
            'options'  => $this->getConfig()->status,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Generates status filter buttons
     *
     * @param array $config [optional]
     *
     * @return  string  html
     */
    public function filterList(array $config = array())
    {
        $status = $this->getConfig()->statusFilters;

        // Merge with user-defined status
        if ($config['status']) {
            $status = $status->merge($config['status']);
        }

        $result = '';

        foreach ($status as $value => $label)
        {
            $class = ($config['active_status'] == $value) ? 'class="active"' : null;
            $href  = ($config['active_status'] <> $value) ? 'href="' . $this->getTemplate()->route("status={$value}") . '"' : null;
            $label = $this->getObject('translator')->translate($label);

            $result .= "<a {$class} {$href}>{$label}</a>";
        }

        return $result;
    }
}
