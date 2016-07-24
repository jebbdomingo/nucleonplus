<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmCompensation extends KObject
{
    /**
     * The status of the reward 
     *
     * @var string
     */
    protected $_reward_active_status;

    /**
     * Direct Referral
     *
     * @var string
     */
    protected $_directreferral;

    /**
     * Patronage
     *
     * @var string
     */
    protected $_patronage;

    /**
     * Unilevel
     *
     * @var string
     */
    protected $_unilevel;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_directreferral       = $this->getObject($config->directreferral);
        $this->_patronage            = $this->getObject($config->patronage);
        $this->_unilevel             = $this->getObject($config->unilevel);
        $this->_reward_active_status = $config->reward_active_status;
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
            'directreferral' => 'com:nucleonplus.mlm.packagedirectreferral',
            'patronage'      => 'com:nucleonplus.mlm.packagepatronage',
            'unilevel'       => 'com:nucleonplus.mlm.packagereferral',
            'reward_active_status' => 'active', // Reward's active status
        ));

        parent::_initialize($config);
    }

    /**
     * Create corresponding slots in the Rewards system
     *
     * @param KModelEntityInterface $reward
     *
     * @return void
     */
    public function create(KModelEntityInterface $reward)
    {
        // Create the slots only if the reward is not yet activated
        if ($reward->status <> $this->_reward_active_status)
        {
            // Create corresponding slots for this order/reward
            $slot = $this->_patronage->createSlots($reward);
            die('test');

            if ($this->_directreferral->create($slot) === false) {
                // Connect to other slot
                $this->_patronage->connectToOtherSlot($slot);
            }

            // Create unilevel bonuses (direct and indirect referrals)
            $this->_unilevel->create($reward);
        }
        else throw new KControllerExceptionRequestInvalid('MLM Compensation: Invalid Request');

        die('testing');

        return false;
    }
}
