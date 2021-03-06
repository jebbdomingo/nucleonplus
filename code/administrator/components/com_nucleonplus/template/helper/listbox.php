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
    /**
     * Order State Filters
     *
     * @var array
     */
    protected $_orderStatusFilters = [];

    /**
     * Payment methods
     *
     * @var array
     */
    protected $_paymentMethods = [];

    /**
     * Shipping methods
     *
     * @var array
     */
    protected $_shippingMethods = [];

    /**
     * Payout status
     *
     * @var array
     */
    protected $_payoutStatus = [];

    /**
     * Savings types
     *
     * @var array
     */
    protected $_bank_account_types = [];

    /**
     * State province
     *
     * @var array
     */
    protected $_state_province = [];

    /**
     * Constructor
     *
     * @param KObjectConfig $config [description]
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_orderStatusFilters  = $config->orderStatusFilters;
        $this->_paymentMethods      = $config->paymentMethods;
        $this->_shippingMethods     = $config->shippingMethods;
        $this->_payoutStatus        = $config->payoutStatus;
        $this->_bank_account_types  = $config->bank_account_types;
        $this->_state_province      = $config->state_province;
    }

    /**
     * Initialization
     *
     * @param KObjectConfig $config
     *
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        // Status
        $config
        ->append(array(
            'status' => array(
                array('label' => 'New', 'value' => 'new'),
                array('label' => 'Pending', 'value' => 'pending'),
                array('label' => 'Active', 'value' => 'active'),
                array('label' => 'Terminated', 'value' => 'terminated'),
                array('label' => 'Closed', 'value' => 'closed')
            )
        ))
        ->append(array(
            'statusFilters' => array(
                'all'        => 'All',
                'new'        => 'New',
                'pending'    => 'Pending',
                'active'     => 'Active',
                'terminated' => 'Terminated',
                'closed'     => 'Closed',
            )
        ))
        ->append(array(
            'invoiceStatus' => array(
                array('label' => 'New', 'value' => 'new'),
                array('label' => 'Sent', 'value' => 'sent'),
                array('label' => 'Paid', 'value' => 'paid'),
            )
        ))
        ->append(array(
            'orderStatus' => array(
                array('label' => 'Pending', 'value' => ComNucleonplusModelEntityOrder::STATUS_PENDING),
                array('label' => 'Awaiting Payment', 'value' => ComNucleonplusModelEntityOrder::STATUS_PAYMENT),
                array('label' => 'Verified', 'value' => ComNucleonplusModelEntityOrder::STATUS_VERIFIED),
                array('label' => 'Processing', 'value' => ComNucleonplusModelEntityOrder::STATUS_PROCESSING),
                array('label' => 'Shipped', 'value' => ComNucleonplusModelEntityOrder::STATUS_SHIPPED),
                array('label' => 'Delivered', 'value' => ComNucleonplusModelEntityOrder::STATUS_DELIVERED),
                array('label' => 'Cancelled', 'value' => ComNucleonplusModelEntityOrder::STATUS_CANCELLED),
                array('label' => 'Completed', 'value' => ComNucleonplusModelEntityOrder::STATUS_COMPLETED),
            )
        ))
        ->append(array(
            'orderStatusFilters' => array(
                'all'                                             => 'All',
                ComNucleonplusModelEntityOrder::STATUS_PENDING    => 'Pending',
                ComNucleonplusModelEntityOrder::STATUS_PAYMENT    => 'Awaiting Payment',
                ComNucleonplusModelEntityOrder::STATUS_VERIFIED   => 'Verified',
                ComNucleonplusModelEntityOrder::STATUS_PROCESSING => 'Processing',
                ComNucleonplusModelEntityOrder::STATUS_SHIPPED    => 'Shipped',
                ComNucleonplusModelEntityOrder::STATUS_DELIVERED  => 'Delivered',
                ComNucleonplusModelEntityOrder::STATUS_CANCELLED  => 'Cancelled',
                ComNucleonplusModelEntityOrder::STATUS_COMPLETED  => 'Completed',
            )
        ))
        ->append(array(
            'paymentMethods' => array(
                array('label' => 'Cash', 'value' => 'cash'),
                array('label' => 'Bank Deposit', 'value' => 'deposit')
            )
        ))
        ->append(array(
            'shippingMethods' => array(
                array('label' => 'N/A', 'value' => 'na'),
                array('label' => 'XEND', 'value' => 'xend'),
                array('label' => 'Pick-up', 'value' => 'pickup')
            )
        ))
        ->append(array(
            'payoutStatus' => array(
                array('label' => 'Pending', 'value' => 'pending'),
                array('label' => 'Processing', 'value' => 'processing'),
                array('label' => 'Check Generated', 'value' => 'checkgenerated'),
                array('label' => 'Disbursed', 'value' => 'disbursed'),
            )
        ))
        ->append(array(
            'bank_account_types' => array(
                array('label' => 'Select', 'value' => null),
                array('label' => 'Savings', 'value' => 'savings'),
                array('label' => 'Check', 'value' => 'check'),
            )
        ))
        ->append(array(
            'state_province' => array(
                array('label' => 'Metro Manila', 'value' => 'metro_manila'),
                array('label' => 'Luzon', 'value' => 'luzon'),
                array('label' => 'Visayas', 'value' => 'visayas'),
                array('label' => 'Mindanao', 'value' => 'mindanao'),
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Generates invoice status list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function invoiceStatus(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'invoice_status',
            'selected' => null,
            'options'  => $this->getConfig()->invoiceStatus,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Generates order status list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function orderStatus(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'order_status',
            'selected' => null,
            'options'  => $this->getConfig()->orderStatus,
            'filter'   => array()
        ));

        return parent::optionlist($config);
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
        if (isset($config['status']) && $config['status']) {
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
     * Generates order status filter buttons
     *
     * @todo rename to status filter list
     *
     * @param array $config [optional]
     *
     * @return  string  html
     */
    public function orderStatusFilter(array $config = array())
    {
        $status = $this->_orderStatusFilters;

        // Merge with user-defined status
        if (isset($config['status']) && $config['status']) {
            $status = $status->merge($config['status']);
        }

        $result = '';

        foreach ($status as $value => $label)
        {
            $class = ($config['active_status'] == $value) ? 'class="active"' : null;
            $href  = ($config['active_status'] <> $value) ? 'href="' . $this->getTemplate()->route("order_status={$value}") . '"' : null;
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
        $config = new KObjectConfigJson($config);

        $items   = $this->getObject('com:qbsync.model.items')->fetch();
        $options = array();

        foreach ($items as $item)
        {
            if (!in_array($item->Type, ComQbsyncModelEntityItem::$item_types)) {
                continue;
            }

            $options[] = array('label' => "{$item->Name} | PHP {$item->UnitPrice} | ItemRef:{$item->ItemRef}", 'value' => $item->ItemRef);
        }

        $config->append(array(
            'name'     => 'ItemRef',
            'selected' => null,
            'options'  => $options,
            'filter'   => array(),
            'select2'  => true,
            'deselect' => true,
        ));

        return parent::optionlist($config);
    }

    /**
     * Provides an accounts autocomplete select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @return string The autocomplete users select box.
     */
    public function accounts($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'    => 'accounts',
            'value'    => 'id',
            'label'    => 'account_number',
            'sort'     => 'id',
            'validate' => false
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
        // Override options
        if ($config['paymentMethods']) {
            $this->_paymentMethods = $config['paymentMethods'];
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'payment_method',
            'selected' => null,
            'options'  => $this->_paymentMethods,
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
        // Override options
        if ($config['shippingMethods']) {
            $this->_shippingMethods = $config['shippingMethods'];
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'shipping_method',
            'selected' => null,
            'options'  => $this->_shippingMethods,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Generates payout status list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function payoutStatus(array $config = array())
    {
        // Override options
        if ($config['payoutStatus']) {
            $this->_payoutStatus = $config['payoutStatus'];
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'status',
            'selected' => null,
            'options'  => $this->_payoutStatus,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * Generates payout status filter buttons
     *
     * @param array $config [optional]
     *
     * @return  string  html
     */
    public function payoutStatusFilter(array $config = array())
    {
        $result = null;

        foreach (ComNucleonplusModelEntityPayout::$payout_status as $value => $label)
        {
            $class = ($config['active'] == $value) ? 'class="active"' : null;
            $href  = ($config['active'] <> $value) ? 'href="' . $this->getTemplate()->route("status={$value}") . '"' : null;
            $label = $this->getObject('translator')->translate($label);

            $result .= "<a {$class} {$href}>{$label}</a>";
        }

        return $result;
    }

    /**
     * Generates payout methods filter buttons
     *
     * @param array $config [optional]
     *
     * @return  string  html
     */
    public function payoutMethodsFilter(array $config = array())
    {
        $payoutMethods = array(
            ComNucleonplusModelEntityPayout::PAYOUT_METHOD_FUNDS_TRANSFER => 'Funds Transfer',
            ComNucleonplusModelEntityPayout::PAYOUT_METHOD_PICKUP         => 'Pick-up'
        );

        $result = null;

        foreach ($payoutMethods as $value => $label)
        {
            $class = ($config['active'] == $value) ? 'class="active"' : null;
            $href  = ($config['active'] <> $value) ? 'href="' . $this->getTemplate()->route("payout_method={$value}") . '"' : null;
            $label = $this->getObject('translator')->translate($label);

            $result .= "<a {$class} {$href}>{$label}</a>";
        }

        return $result;
    }

    /**
     * Bank account types list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function bankAccountTypes(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'bank_account_type',
            'selected' => null,
            'options'  => $this->_bank_account_types,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }

    /**
     * State province list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function stateProvince($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'state',
            'selected' => null,
            'options'  => $this->_state_province
        ));

        return parent::optionlist($config);
    }

    /**
     * Provides cities select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * 
     * @return string The autocomplete select box.
     */
    public function cities($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'    => 'cities',
            'value'    => 'id',
            'label'    => '_name',
            'sort'     => '_name',
            'validate' => false,
        ));

        return $this->_autocomplete($config);
    }

    public function payoutMethods(array $config = array())
    {
        $options = array(
            // array('label' => 'Pick-up', 'value' => ComNucleonplusModelEntityPayout::PAYOUT_METHOD_PICKUP),
            array('label' => 'Funds Transfer', 'value' => ComNucleonplusModelEntityPayout::PAYOUT_METHOD_FUNDS_TRANSFER)
        );

        // Override options
        if (isset($config['payout_methods']) && !empty($config['payout_methods'])) {
            $options = $config['payout_methods'];
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'payout_method',
            'selected' => null,
            'options'  => $options,
            'filter'   => array()
        ));

        return parent::radiolist($config);
    }

    public function banks(array $config = array())
    {
        $options = array(
            array('label' => 'BDO', 'value' => 'bdo')
        );

        // Override options
        if (isset($config['banks']) && !empty($config['banks'])) {
            $options = $config['banks'];
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'bank',
            'selected' => null,
            'options'  => $options,
            'filter'   => array()
        ));

        return parent::optionlist($config);
    }
}
