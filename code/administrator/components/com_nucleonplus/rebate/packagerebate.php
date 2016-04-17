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
class ComNucleonplusRebatePackagerebate extends KObject
{
    /**
     * Slot controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Slot model identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_model;

    /**
     * Identifier of the Reward model
     *
     * @var string
     */
    protected $_reward_model;

    /**
     * The status of the reward 
     *
     * @var string
     */
    protected $_reward_active_status;

    /**
     * Slots
     *
     * @var array
     */
    private $slots = array();

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller           = $config->controller;
        $this->_model                = $config->model;
        $this->_reward_model         = $config->reward_model;
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
            'controller'           => 'com:nucleonplus.controller.slot',
            'model'                => 'com:nucleonplus.model.slots',
            'reward_model'         => 'com:nucleonplus.model.rewards',
            'reward_active_status' => 'active', // Reward's active status
            'item_model'           => 'com:nucleonplus.model.packages', // Product or Item object's identifier
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
            $reward->status = $this->_reward_active_status;
            $reward->save();

            // Create and organize member's own set of slots
            $slot = $this->createOwnSlots($reward);

            // Connect the member's primary slot to an available slot of other members in the rewards sytem
            $this->connectToOtherSlot($slot);

            return true;
        }
        else throw new KControllerExceptionRequestInvalid('Rebate Package: Invalid Request');

        return false;
    }

    /**
     * Create and organize own slots
     *
     * @param KModelEntityInterface $reward
     *
     * @return KModelEntityInterface Primary Slot
     */
    private function createOwnSlots(KModelEntityInterface $reward)
    {
        $slots = array();

        for ($i=0; $i < $reward->slots; $i++)
        {
            $slot             = $this->createSlot($reward);
            $slots[$i]        = $slot;
            $unpaidParentSlot = $this->getOwnUnpaidSlot($slots);

            if ($unpaidParentSlot->id == $slot->id) {
                // Make sure it's not matching to itself
                continue;
            }

            // Match succeeding slots to earlier (unpaid) slots
            $this->allocateOwnSlot($unpaidParentSlot, $slot);
        }

        return $slots[0];
    }

    /**
     * Slot factory
     *
     * @param KModelEntityInterface $reward
     *
     * @return KModelEntityInterface
     */
    private function createSlot(KModelEntityInterface $reward)
    {
        $controller = $this->getObject($this->_controller);

        $data['reward_id'] = $reward->id;

        return $controller->add($data);
    }

    /**
     * Place member's own slots
     *
     * @param KModelEntityRow $unpaidParentSlot
     * @param KModelEntityRow $slot
     *
     * @return void
     */
    private function allocateOwnSlot(KModelEntityRow $unpaidParentSlot, KModelEntityRow $slot)
    {
        // Match the current slot to either left or right leg of the previous (unpaid) slot
        if ($unpaidParentSlot && is_null($unpaidParentSlot->lf_slot_id)) {
            // Place to the left leg of the parent slot
            $unpaidParentSlot->lf_slot_id = $slot->id;
            $unpaidParentSlot->save();
            $slot->consume();
        } elseif ($unpaidParentSlot && is_null($unpaidParentSlot->rt_slot_id)) {
            // Place to the right leg of the parent slot
            $unpaidParentSlot->rt_slot_id = $slot->id;
            $unpaidParentSlot->save();
            $slot->consume();
        }
    }

    /**
     * Get member's own unpaid slot from set of slots
     *
     * @param array $slots
     * 
     * @return KModelEntityRow
     */
    private function getOwnUnpaidSlot($slots)
    {
        foreach ($slots as $key => $slot) {
            if (is_null($slot->lf_slot_id) || is_null($slot->rt_slot_id)) {
                return $slot;
            }
        }
    }

    /**
     * Connect the member's primary slot to an available slot of other members in the rewards sytem
     *
     * @param KModelEntityRow $slot The member's first slot in his set of slots based on his product package purchase
     *
     * @return void
     */
    private function connectToOtherSlot(KModelEntityRow $slot)
    {
        // All the slots from the rewards system
        if ($unpaidSlot = $this->getObject($this->_model)->reward_id($slot->reward_id)->getUnpaidSlots())
        {
            $unpaidSlot->{$unpaidSlot->available_leg} = $slot->id;
            $unpaidSlot->save();
            $slot->consume();

            // Process member rebates
            // TODO move to dedicated rewards processing method
            $reward = $this->getObject($this->_reward_model)->id($unpaidSlot->reward_id)->fetch();
            $reward->processRebate();
        }
        else 
        {
            $slot->flushOut();
        }
    }
}