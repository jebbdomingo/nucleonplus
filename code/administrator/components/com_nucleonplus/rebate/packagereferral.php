<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusRebatePackagereferral extends KObject
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
            'controller'     => 'com:nucleonplus.controller.referralbonuses',
            'unilevel_count' => 10
        ));

        parent::_initialize($config);
    }

    /**
     * Record referral bonus payouts
     *
     * @param KModelEntityInterface $orders
     *
     * @return void
     */
    public function create(KModelEntityInterface $orders)
    {
        foreach ($orders as $order)
        {
            $account = $this->getObject('com:nucleonplus.model.accounts')->id($order->account_id)->fetch();

            $this->_recordDirectReferrals($account, $order);
        }
    }

    /**
     * Record direct referrals
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    private function _recordDirectReferrals(KModelEntityInterface $account, KModelEntityInterface $order)
    {
        $controller = $this->getObject($this->_controller);

        if (is_null($account->sponsor_id)) {
            return null;
        }

        $data = [
            'reward_id'     => $order->_reward_id,
            'account_id'    => $account->getIdFromSponsor(),
            'referral_type' => 'dr', // Direct Referral
            'points'        => ($order->_reward_drpv * $order->_reward_slots)
        ];

        $controller->add($data);

        // Check if direct referrer has sponsor as well
        $directReferrer = $this->getObject('com:nucleonplus.model.accounts')->id($account->getIdFromSponsor())->fetch();

        if (!is_null($directReferrer->sponsor_id)) {
            $this->_recordIndirectReferrals($directReferrer, $order);
        }
    }

    /**
     * Record indirect referrals
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    private function _recordIndirectReferrals(KModelEntityInterface $directReferrer, KModelEntityInterface $order)
    {
        $controller = $this->getObject($this->_controller);

        $indirectReferrer = $this->getObject('com:nucleonplus.model.accounts')->id($directReferrer->getIdFromSponsor())->fetch();

        $data = [
            'reward_id'     => $order->_reward_id,
            'account_id'    => $indirectReferrer->id,
            'referral_type' => 'ir', // Indirect Referral
            'points'        => ($order->_reward_irpv * $order->_reward_slots)
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
                'reward_id'     => $order->_reward_id,
                'account_id'    => $indirectReferrer->id,
                'referral_type' => 'ir', // Indirect Referral
                'points'        => ($order->_reward_irpv * $order->_reward_slots)
            ];
            
            $controller->add($data);
        }
    }
}