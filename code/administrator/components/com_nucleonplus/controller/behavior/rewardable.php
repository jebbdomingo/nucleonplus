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
class ComNucleonplusControllerBehaviorRewardable extends KControllerBehaviorEditable
{
    /**
     * Slot controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

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

        $this->_product_column = KObjectConfig::unbox($config->product_column);
        $this->_account_column = KObjectConfig::unbox($config->account_column);

        $this->_controller = $config->controller;
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
            'controller' => 'com:nucleonplus.controller.slot',
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
            $package = $this->getObject('com:nucleonplus.model.packages')->id($order->package_id)->fetch();

            // Create and organize member's own set of slots
            $slot = $this->createOwnSlots($order, $package->slots);

            // Connect the member's primary slot to an available slot of other members in the rewards sytem
            $this->connectToOtherSlot($slot);
        }
    }

    /**
     * Create and organize own slots
     *
     * @param KModelEntityRow $order
     * @param integer         $num_slots
     *
     * @return KModelEntityRow Primary Slot
     */
    private function createOwnSlots($order, $num_slots)
    {
        $slots = array();

        for ($i=0; $i < $num_slots; $i++)
        {
            $slot             = $this->createSlot($order);
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
     * @param KModelEntityRow $order
     *
     * @return KModelEntityRow
     */
    private function createSlot(KModelEntityRow $order)
    {
        $controller = $this->getObject($this->_controller);

        $data['rebate_id'] = $order->_rebate_id;

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
            $slot->consumed = 1;
            $slot->save();
        } elseif ($unpaidParentSlot && is_null($unpaidParentSlot->rt_slot_id)) {
            // Place to the right leg of the parent slot
            $unpaidParentSlot->rt_slot_id = $slot->id;
            $unpaidParentSlot->save();
            $slot->consumed = 1;
            $slot->save();
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
        if ($slots = $this->getObject('com:nucleonplus.model.slots')->rebate_id($slot->rebate_id)->getUnpaidSlots())
        {
            $slots->{$slots->available_leg} = $slot->id;
            $slots->save();
            $slot->consumed = 1;
            $slot->save();
            
            // Process member rebates
            // @todo move to dedicated rewards processing method
            $rebate = $this->getObject('com:nucleonplus.model.rebates')->id($slots->rebate_id)->fetch();
            $rebate->processRebate();
        }
    }
}