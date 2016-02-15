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
 * Used by the order controller to create entries in the rewards system upon payment of order
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 */
class ComNucleonplusControllerBehaviorRewardable extends KControllerBehaviorAbstract
{
    /**
     * Rebate types queue.
     *
     * @var KObjectQueue
     */
    private $__queue;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Create the logger queue
        $this->__queue = $this->getObject('lib:object.queue');

        // Attach the loggers
        $reward_types = KObjectConfig::unbox($config->reward_types);

        foreach ($reward_types as $type) {
            $this->attachRewardType($type);
        }
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
            'priority'     => self::PRIORITY_LOWEST,
            'reward_types' => array(),
        ));

        // Append the default rebate system if none is set.
        if (!count($config->reward_types)) {
            $config->append(array('reward_types' => array('com:nucleonplus.rebate.packagereward')));
        }

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
            foreach($this->__queue as $reward) {
                $reward->create($order);
            }
        }
    }

    /**
     * Attach a type of Reward system.
     *
     * @param mixed $rewardType An object that implements ObjectInterface, ObjectIdentifier object or valid identifier
     *                      string.
     * @return this
     */
    public function attachRewardType($rewardType, $config = array())
    {
        $identifier = $this->getIdentifier($rewardType);
        
        if (!$this->__queue->hasIdentifier($identifier))
        {
            $rewardType = $this->getObject($identifier);

            $this->__queue->enqueue($rewardType, self::PRIORITY_NORMAL);
        }

        return $this;
    }
}