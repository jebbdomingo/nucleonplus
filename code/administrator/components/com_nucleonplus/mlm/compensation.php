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
     * Direct Referral Package
     *
     * @var string
     */
    protected $_directreferral_package;

    /**
     * Direct Referral Retail
     *
     * @var string
     */
    protected $_directreferral_retail;

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
     * Rebates
     *
     * @var string
     */
    protected $_rebates;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_reward_active_status   = $config->reward_active_status;
        $this->_directreferral_package = $this->getObject($config->directreferral_package);
        $this->_directreferral_retail  = $this->getObject($config->directreferral_retail);
        $this->_patronage              = $this->getObject($config->patronage);
        $this->_unilevel               = $this->getObject($config->unilevel);
        $this->_rebates                = $this->getObject($config->rebates);
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
            'directreferral_package' => 'com:nucleonplus.mlm.directreferralpackage',
            'directreferral_retail'  => 'com:nucleonplus.mlm.directreferralretail',
            'patronage'              => 'com:nucleonplus.mlm.packagepatronage',
            'unilevel'               => 'com:nucleonplus.mlm.packageunilevel',
            'rebates'                => 'com:nucleonplus.mlm.packagerebates',
            'reward_active_status'   => 'active', // Reward's active status
        ));

        parent::_initialize($config);
    }

    /**
     * Create corresponding package or retail reward
     *
     * @param KModelEntityInterface $reward
     *
     * @return void
     */
    public function create(KModelEntityInterface $reward)
    {
        // Create the commission only if the reward is not yet activated
        if ($reward->status <> $this->_reward_active_status)
            {
            if ($reward->type == ComNucleonplusModelEntityReward::REWARD_PACKAGE) {
                $this->_createPackageCompensation($reward);
            } else {
                $this->_createRetailCompensation($reward);
            }
        }
        }
        else throw new KControllerExceptionRequestInvalid('MLM Compensation: Invalid Request');
    }

    /**
     * Create package purchase reward, create corresponding slots in the Rewards system
     *
     * @param KModelEntityInterface $reward
     *
     * @return void
     */
    protected function _createPackageCompensation($reward)
    {
        // Create corresponding slots for this order/reward
        $slot = $this->_patronage->createSlots($reward);

        // Package direct referral bonus
        if ($this->_directreferral_package->create($slot) === false) {
            // Connect to other slot
            $this->_patronage->connectToOtherSlot($slot);
        }

        // Create unilevel bonuses (direct and indirect referrals)
        $this->_unilevel->create($reward);

        // Create rebates
        $this->_rebates->create($reward);
    }

    /**
     * Create retail purchase reward
     *
     * @param [type] $reward [description]
     *
     * @return [type] [description]
     */
    protected function _createRetailCompensation($reward)
    {
        // Retail direct referral bonus
        $this->_directreferral_retail->create($reward);

        // Create unilevel bonuses (direct and indirect referrals)
        $this->_unilevel->create($reward);

        // Create rebates
        $this->_rebates->create($reward);
    }
}
