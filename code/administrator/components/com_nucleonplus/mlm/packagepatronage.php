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
class ComNucleonplusMlmPackagepatronage extends KObject
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
     * Accounting Service
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_accounting_service;

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

        // Accounting service
        $identifier = $this->getIdentifier($config->accounting_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceTransferInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceTransferInterface"
            );
        }
        else $this->_accounting_service = $service;
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
            'reward_active_status' => ComNucleonplusModelEntityReward::STATUS_ACTIVE, // Reward's active status
            'item_model'           => 'com:nucleonplus.model.packages', // Product or Item object's identifier
            'accounting_service'   => 'com:nucleonplus.accounting.service.transfer'
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
    public function createSlots(KModelEntityInterface $reward)
    {
        // Create the slots only if the reward is not yet activated
        if ($reward->status <> $this->_reward_active_status)
        {
            // Create and organize member's own set of slots
            $slot = $this->_createOwnSlots($reward);

            return $slot;
        }
        else throw new KControllerExceptionRequestInvalid('Patronage Package: Invalid Request');

        return false;
    }

    /**
     * Create and organize own slots
     *
     * @param KModelEntityInterface $reward
     *
     * @return KModelEntityInterface Primary Slot
     */
    private function _createOwnSlots(KModelEntityInterface $reward)
    {
        $slots = array();

        for ($i=0; $i < $reward->slots; $i++)
        {
            $slot             = $this->_createSlot($reward);
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
    protected function _createSlot(KModelEntityInterface $reward)
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
            $this->_payOtherSlot($slot);
        } elseif ($unpaidParentSlot && is_null($unpaidParentSlot->rt_slot_id)) {
            // Place to the right leg of the parent slot
            $unpaidParentSlot->rt_slot_id = $slot->id;
            $unpaidParentSlot->save();
            $this->_payOtherSlot($slot);
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
     * @return null|void
     */
    public function connectToOtherSlot(KModelEntityRow $slot)
    {
        // Fetch an active reward which is next in line for payout
        $reward = $this->getObject($this->_reward_model)->getNextActiveReward($slot->reward_id);

        // Validate the reward
        if (count($reward) == 0)
        {
            // If no reward which is next in line, flush-out the slot and terminate the processing
            $this->_flushOut($slot);

            return null;
        }
        // Avoid matching the slot to the same reward
        elseif ($reward->id == $slot->reward_id) return null;

        // Pending slots == slots without left or right leg matches
        $pendingSlots = $this->getObject($this->_model)->reward_id($reward->id)->fetch();

        if (count($pendingSlots) > 0)
        {
            foreach ($pendingSlots as $pendingSlot)
            {
                if ($pendingSlot->lf_slot_id == 0 || $pendingSlot->rt_slot_id == 0)
                {
                    if ($pendingSlot->lf_slot_id == 0) {
                        $pendingSlot->lf_slot_id = $slot->id;
                    } elseif ($pendingSlot->rt_slot_id == 0) {
                        $pendingSlot->rt_slot_id = $slot->id;
                    }

                    $pendingSlot->save();
                    $this->_payOtherSlot($slot);

                    // Process member patronage
                    $pendingSlot->getReward()->processPatronage();

                    break;
                }
            }
        }
        // TODO check if this is still needed since there's no active reward without a slot
        else $this->_flushOut($slot);
    }

    /**
     * Pay other slot from the slot
     * Mark the slot as consumed i.e. it is allocated to an upline slot
     *
     * @return void
     */
    protected function _payOtherSlot($slot)
    {
        if ($slot->consume())
        {
            $reward = $slot->getReward();
            $this->_accounting_service->allocatePatronage($reward->product_id, $reward->prpv);
        }
    }

    /**
     * Flush the slot's prpv out
     * Usually when there's no upline slot available
     *
     * @return [type] [description]
     */
    protected function _flushOut($slot)
    {
        if ($slot->consume())
        {
            $reward = $slot->getReward();
            $this->_accounting_service->allocateSurplusPatronage($reward->product_id, $reward->prpv);
        }
    }
}
