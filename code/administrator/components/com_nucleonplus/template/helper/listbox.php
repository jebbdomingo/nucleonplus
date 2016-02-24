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
        // Status
        $config
        ->append(array(
            'status' => array(
                array('label' => 'New', 'value' => 'new'),
                array('label' => 'Active', 'value' => 'active'),
                array('label' => 'Terminated', 'value' => 'terminated'),
                array('label' => 'Closed', 'value' => 'closed')
            )
        ))
        ->append(array(
            'statusFilters' => array(
                'all'        => 'All',
                'new'        => 'New',
                'active'     => 'Active',
                'terminated' => 'Terminated',
                'closed'     => 'Closed'
            )
        ))
        ->append(array(
            'paymentMethods' => array(
                array('label' => 'Bank Deposit', 'value' => 'deposit'),
                array('label' => 'Cash', 'value' => 'cash')
            )
        ))
        ->append(array(
            'shippingMethods' => array(
                array('label' => 'XEND', 'value' => 'xend'),
                array('label' => 'Pick-up', 'value' => 'pickup')
            )
        ));

        // Product packages
        $packages = [];
        foreach ($this->getObject('com:nucleonplus.model.packages')->fetch() as $package) {
            $packages[] = [
                'label' => "{$package->name} (slots: {$package->_rewardpackage_slots}) Php {$package->price}",
                'value' => $package->id
            ];
        }
        $config->append(['packages' => $packages]);

        parent::_initialize($config);
    }

    /**
     * Generates status list box
     *
     * @todo rename to status list
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
     * @todo rename to status filter list
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

    /**
     * Generates product list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function productList($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'status',
            'selected' => null,
            'options'  => $this->getConfig()->packages,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Provides a users select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @return string The autocomplete users select box.
     */
    public function accounts($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'        => 'accounts',
            'name'         => 'account',
            'value'        => 'id',
            'label'        => 'account_name',
            'sort'         => 'account_name',
            'validate'     => false
        ));

        return $this->_autocomplete($config);
    }

    /**
     * Generates payment method list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function paymentMethods(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'payment_method',
            'selected' => null,
            'options'  => $this->getConfig()->paymentMethods,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Generates shipping method list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function shippingMethods(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'shipping_method',
            'selected' => null,
            'options'  => $this->getConfig()->shippingMethods,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }
}