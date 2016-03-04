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
     * Transaction controller identifier.
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
            'controller'     => 'com:nucleonplus.controller.transaction',
            'unilevel_count' => 10
        ));

        parent::_initialize($config);
    }

    /**
     * Create referral bonus transactions upon payment of the Order
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        $this->_recordReferrals($context->result); // Orders
    }

    /**
     * Create referral bonus transactions upon payment of the Order
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _afterEdit(KControllerContextInterface $context)
    {
        $this->_recordReferrals($context->result); // Orders
    }

    /**
     * Record referral bonus payouts
     *
     * @param KModelEntityInterface $orders
     *
     * @return void
     */
    protected function _recordReferrals(KModelEntityInterface $orders)
    {
        foreach ($orders as $order)
        {
            $this->_recordDirectReferrals($order);
            $this->_recordIndirectReferrals($order);
        }
    }

    /**
     * Create referral bonus transactions upon payment of the Order
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    /*protected function _afterVerifypayment(KControllerContextInterface $context)
    {
        $orders = $context->result; // Order entity

        foreach ($orders as $order)
        {
            $this->_recordDirectReferrals($order);
            $this->_recordIndirectReferrals($order);
        }
    }*/

    /**
     * Record direct referrals
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    private function _recordDirectReferrals(KModelEntityInterface $order)
    {
        $controller = $this->getObject($this->_controller);
        $account    = $this->getObject('com:nucleonplus.model.accounts')->id($order->account_id)->fetch();

        if (is_null($account->sponsor_id)) {
            return null;
        }

        $data = [
            'reward_id'   => $order->_reward_id,
            'account_id'  => $account->getIdFromSponsor(),
            'reward_type' => 'dr', // Direct Referral
            'credit'      => ($order->_reward_drpv * $order->_reward_slots)
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

        $directReferrer = $this->getObject('com:nucleonplus.model.accounts')->id($account->getIdFromSponsor())->fetch();

        if (is_null($directReferrer->sponsor_id)) {
            return null;
        }

        $indirectReferrer = $this->getObject('com:nucleonplus.model.accounts')->id($directReferrer->getIdFromSponsor())->fetch();

        $data = [
            'reward_id'   => $order->_reward_id,
            'account_id'  => $indirectReferrer->id,
            'reward_type' => 'ir', // Indirect Referral
            'credit'      => ($order->_reward_irpv * $order->_reward_slots)
        ];

        // Record pay for the first immediate referrer
        $controller->add($data);

        // Try to get referrers up to the 10th level
        for ($x = 0; $x < ($this->_unilevel_count - 1); $x++)
        {
            // Terminate execution if the immediate indirect referrer has no sponsor
            // i.e. there are no other indirect referrers to pay
            if (is_null($indirectReferrer->sponsor_id)) {
                return null;
            }

            $indirectReferrer = $this->getObject('com:nucleonplus.model.accounts')->id($indirectReferrer->getIdFromSponsor())->fetch();

            $data = [
                'reward_id'   => $order->_reward_id,
                'account_id'  => $indirectReferrer->id,
                'reward_type' => 'ir', // Indirect Referral
                'credit'      => ($order->_reward_irpv * $order->_reward_slots)
            ];
            
            $controller->add($data);
        }
    }
}