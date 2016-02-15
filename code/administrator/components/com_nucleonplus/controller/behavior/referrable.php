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
 * 
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 */
class ComNucleonplusControllerBehaviorReferrable extends KControllerBehaviorAbstract
{
    /**
     * Rebate controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Number of levels for direct referrals
     *
     * @param integer
     */
    protected $_unilevel_count;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller     = $config->controller;
        $this->_unilevel_count = $config->unilevel_count;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller'     => 'com:nucleonplus.controller.referral',
            'unilevel_count' => 10
        ));

        parent::_initialize($config);
    }

    /**
     * Create an entry to the Rewards system upon payment of the Order
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _afterMarkpaid(KControllerContextInterface $context)
    {
        $orders = $context->result; // Order entity

        foreach ($orders as $order)
        {
            $this->_recordDirectReferrals($order);
            $this->_recordIndirectReferrals($order);
        }
    }

    /**
     * Record direct referrals
     *
     * @param KModelEntityRow $order
     *
     * @return void
     */
    private function _recordDirectReferrals(KModelEntityRow $order)
    {
        $controller = $this->getObject($this->_controller);
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($order->account_id)->fetch();

        if (is_null($account->sponsor_id)) {
            return null;
        }

        $data = [
            'order_id'      => $order->id,
            'sponsor_id'    => $account->sponsor_id,
            'referral_type' => 'dr', // Direct Referral
            'points'        => ($order->_rebate_drpv * $order->_rebate_slots)
        ];

        $controller->add($data);
    }

    /**
     * Record indirect referrals
     *
     * @param KModelEntityRow $order
     *
     * @return void
     */
    private function _recordIndirectReferrals(KModelEntityRow $order)
    {
        $controller = $this->getObject($this->_controller);
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($order->account_id)->fetch();

        if (is_null($account->sponsor_id)) {
            return null;
        }

        $directReferrer = $this->getObject('com:nucleonplus.model.accounts')->account_number($account->sponsor_id)->fetch();

        if (is_null($directReferrer->sponsor_id)) {
            return null;
        }

        $indirectReferrer = $this->getObject('com:nucleonplus.model.accounts')->account_number($directReferrer->sponsor_id)->fetch();

        $data = [
            'order_id'      => $order->id,
            'sponsor_id'    => $indirectReferrer->sponsor_id,
            'referral_type' => 'ir', // Indirect Referral
            'points'        => ($order->_rebate_irpv * $order->_rebate_slots)
        ];

        $controller->add($data);

        // Try to get referrers up to 10th level
        for ($x = 0; $x < ($this->_unilevel_count - 1); $x++)
        {
            $indirectReferrer = $this->getObject('com:nucleonplus.model.accounts')->account_number($indirectReferrer->sponsor_id)->fetch();

            $data = [
                'order_id'      => $order->id,
                'sponsor_id'    => $indirectReferrer->sponsor_id,
                'referral_type' => 'ir', // Indirect Referral
                'points'        => ($order->_rebate_irpv * $order->_rebate_slots)
            ];
            
            $controller->add($data);
        }
    }
}