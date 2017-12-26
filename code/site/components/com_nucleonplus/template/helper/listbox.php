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

        foreach (ComRewardlabsModelEntityPayout::$payout_status as $value => $label)
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
            ComRewardlabsModelEntityPayout::PAYOUT_METHOD_FUNDS_TRANSFER => 'Funds Transfer',
            ComRewardlabsModelEntityPayout::PAYOUT_METHOD_PICKUP         => 'Pick-up'
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

    public function payoutMethods(array $config = array())
    {
        $options = array(
            // array('label' => 'Pick-up', 'value' => ComRewardlabsModelEntityPayout::PAYOUT_METHOD_PICKUP),
            array('label' => 'Funds Transfer', 'value' => ComRewardlabsModelEntityPayout::PAYOUT_METHOD_FUNDS_TRANSFER)
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
            'model'        => 'cities',
            'autocomplete' => true,
            'deselect'     => false,
            'prompt'       => '- '.$this->getObject('translator')->translate('Select').' -',
            'value'        => 'id',
            'label'        => 'name',
            'sort'         => '_name',
            'validate'     => false,
        ));

        return parent::_render($config);
    }

    public function banks(array $config = array())
    {
        $options = array(
            array('label' => 'Asia United Bank CA/SA (limited)', 'value' => 'AUB'),
            array('label' => 'Banco de Oro CA/SA', 'value' => 'BDO'),
            array('label' => 'BPI CA/SA', 'value' => 'BPI'),
            array('label' => 'Chinabank CA/SA', 'value' => 'CBC'),
            array('label' => 'EastWest CA/SA', 'value' => 'EWB'),
            array('label' => 'Landbank CA/SA', 'value' => 'LBP'),
            array('label' => 'Metrobank CA/SA', 'value' => 'MBTC'),
            array('label' => 'PNB individual CA/SA', 'value' => 'PNB'),
            array('label' => 'RCBC CA/SA, RCBC Savings Bank CA/SA, RCBC MyWallet', 'value' => 'RCBC'),
            array('label' => 'Security Bank CA/SA', 'value' => 'SBC'),
            array('label' => 'Unionbank CA/SA, EON', 'value' => 'UBP'),
            array('label' => 'UCPB CA/SA', 'value' => 'UCPB'),
            array('label' => 'Cebuana Lhuillier Cash Pick-up', 'value' => 'CEBL'),
            array('label' => 'PSBank CA/SA', 'value' => 'PSB'),
            array('label' => 'Gcash', 'value' => 'GCSH'),
            array('label' => 'Smart Money', 'value' => 'SMRT'),
        );

        // Override options
        if (isset($config['banks']) && !empty($config['banks'])) {
            $options = $config['banks'];
        }

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'bank',
            'selected' => null,
            'select2'  => true,
            'options'  => $options,
            'filter'   => array()
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
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
        $bank_account_types = array(
            array('label' => 'Select', 'value' => null),
            array('label' => 'Savings', 'value' => 'savings'),
            array('label' => 'Check', 'value' => 'check'),
        );

        $config = new KObjectConfig($config);
        $config->append(array(
            'name'     => 'bank_account_type',
            'selected' => null,
            'select2'  => true,
            'options'  => $bank_account_types,
            'filter'   => array()
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }
}
