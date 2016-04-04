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
class ComNucleonplusControllerBehaviorRebatable extends KControllerBehaviorAbstract
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
        $rebate_types = KObjectConfig::unbox($config->rebate_types);

        foreach ($rebate_types as $type) {
            $this->attachRebateType($type);
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
            'rebate_types' => array(),
        ));

        // Append the default rebate system if none is set.
        if (!count($config->rebate_types)) {
            $config->append(array('rebate_types' => array('com:nucleonplus.rebate.packagerebate')));
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
            foreach($this->__queue as $rebate)
            {
                if (!$rebate->create($order)) {
                    continue;
                }
            }
        }
    }

    /**
     * Attach a type of Rebate system.
     *
     * @param mixed $rebateType An object that implements ObjectInterface, ObjectIdentifier object or valid identifier
     *                      string.
     * @return this
     */
    public function attachRebateType($rebateType, $config = array())
    {
        $identifier = $this->getIdentifier($rebateType);
        
        if (!$this->__queue->hasIdentifier($identifier))
        {
            $rebateType = $this->getObject($identifier);

            $this->__queue->enqueue($rebateType, self::PRIORITY_NORMAL);
        }

        return $this;
    }
}