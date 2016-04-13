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
     * Payout status filters
     *
     * @var array
     */
    protected $_payoutStatusFilters = [];

    /**
     * Packages
     *
     * @var array
     */
    protected $_packages = [];

    /**
     * Savings types
     *
     * @var array
     */
    protected $_bank_account_types = [];

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
        $this->_payoutStatusFilters = $config->payoutStatusFilters;
        $this->_packages            = $config->packages;
        $this->_bank_account_types  = $config->bank_account_types;
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
                array('label' => 'Awaiting Payment', 'value' => 'awaiting_payment'),
                array('label' => 'Awaiting Verification', 'value' => 'awaiting_verification'),
                array('label' => 'Processing', 'value' => 'processing'),
                array('label' => 'Shipped', 'value' => 'shipped'),
                array('label' => 'Delivered', 'value' => 'delivered'),
                array('label' => 'Cancelled', 'value' => 'cancelled'),
                array('label' => 'Completed', 'value' => 'completed'),
                array('label' => 'Void', 'value' => 'void'),
            )
        ))
        ->append(array(
            'orderStatusFilters' => array(
                'all'                   => 'All',
                'awaiting_payment'      => 'Awaiting Payment',
                'awaiting_verification' => 'Awaiting Verification',
                'processing'            => 'Processing',
                'shipped'               => 'Shipped',
                'delivered'             => 'Delivered',
                'cancelled'             => 'Cancelled',
                'completed'             => 'Completed',
                'void'                  => 'Void',
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
                array('label' => 'Check Generated', 'value' => 'check_generated'),
                array('label' => 'Disbursed', 'value' => 'disbursed')
            )
        ))
        ->append(array(
            'payoutStatusFilters' => array(
                'all'             => 'All',
                'pending'         => 'Pending',
                'check_generated' => 'Check Generated',
                'disbursed'       => 'Disbursed',
            )
        ))
        ->append(array(
            'bank_account_types' => array(
                array('label' => 'Select', 'value' => null),
                array('label' => 'Savings', 'value' => 'savings'),
                array('label' => 'Check', 'value' => 'check'),
            )
        ));

        // Product packages
        $packages = [];
        foreach ($this->getObject('com:nucleonplus.model.packages')->fetch() as $package) {
            $packages[] = [
                'label' => "{$package->name} P{$package->price} + P{$package->delivery_charge} delivery fee",
                'value' => $package->id
            ];
        }
        $config->append(['packages' => $packages]);

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
        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'status',
            'selected' => null,
            'options'  => $this->_packages,
            'filter'   => array(),
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
     * Provides a product packages autocomplete select box.
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @return string The autocomplete users select box.
     */
    public function packages($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'    => 'packages',
            'value'    => 'id',
            'label'    => 'name',
            'sort'     => 'name',
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
        $status = $this->_payoutStatusFilters;

        // Merge with user-defined status
        if (isset($config['statusFilters']) && $config['statusFilters']) {
            $status = $status->merge($config['statusFilters']);
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
}