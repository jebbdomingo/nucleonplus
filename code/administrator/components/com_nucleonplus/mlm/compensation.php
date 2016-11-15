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
     * Patronage
     *
     * @var string
     */
    protected $_patronage;

    /**
     * Unilevel package
     *
     * @var string
     */
    protected $_unilevel_package;

    /**
     * Unilevel retail
     *
     * @var string
     */
    protected $_unilevel_retail;

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
        $this->_patronage              = $this->getObject($config->patronage);
        $this->_unilevel_package       = $this->getObject($config->unilevel_package);
        $this->_unilevel_retail        = $this->getObject($config->unilevel_retail);
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
            'unilevel_package'       => 'com:nucleonplus.mlm.unilevelpackage',
            'unilevel_retail'        => 'com:nucleonplus.mlm.unilevelretail',
            'patronage'              => 'com:nucleonplus.mlm.packagepatronage',
            'rebates'                => 'com:nucleonplus.mlm.packagerebates',
            'reward_active_status'   => ComNucleonplusModelEntityReward::STATUS_ACTIVE, // Reward's active status
        ));

        parent::_initialize($config);
    }

    /**
     * Create corresponding package or retail reward
     *
     * @param KModelEntityInterface $reward
     *
     * @throws KControllerExceptionRequestInvalid
     *
     * @return boolean
     */
    public function create(KModelEntityInterface $reward)
    {
        $result = false;

        // Create the commission only if the reward is not yet activated
        if ($reward->status <> $this->_reward_active_status)
        {
            if ($reward->type == ComNucleonplusModelEntityReward::REWARD_PACKAGE) {
                $result = $this->_createPackageCompensation($reward);
            } elseif ($reward->type == ComNucleonplusModelEntityReward::REWARD_RETAIL) {
                $result = $this->_createRetailCompensation($reward);
            }
        }
        else throw new KControllerExceptionRequestInvalid('MLM Compensation: Invalid Request');

        return $result;
    }

    /**
     * Create package purchase reward, create corresponding slots in the Rewards system
     *
     * @param KModelEntityInterface $reward
     *
     * @return boolean
     */
    protected function _createPackageCompensation($reward)
    {
        $result = false;

        // Create corresponding slots for this order/reward
        if ($slot = $this->_patronage->createSlots($reward))
        {
            // Package direct referral bonus
            if ($this->_directreferral_package->create($slot) === false) {
                // Connect to other slot
                $this->_patronage->connectToOtherSlot($slot);
            }

            // Create unilevel bonuses (direct and indirect referrals)
            $this->_unilevel_package->create($reward);

            // Create rebates
            $result = $this->_rebates->create($reward);
        }

        if ($result)
        {
            $reward->status = $this->_reward_active_status;
            $reward->save();
        }

        return $result;
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
        $result = false;

        // Create unilevel bonuses (direct and indirect referrals)
        $this->_unilevel_retail->create($reward);

        // Create rebates
        $result = $this->_rebates->create($reward);

        if ($result)
        {
            $reward->status = ComNucleonplusModelEntityReward::STATUS_READY;
            $reward->save();
        }

        return $result;
    }
}
