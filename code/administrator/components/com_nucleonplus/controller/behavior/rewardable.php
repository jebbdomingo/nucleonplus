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
 * Used by the invoice controller to create entries in the marketing system upon payment of invoice
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 */
class ComNucleonplusControllerBehaviorRewardable extends KControllerBehaviorEditable
{
    /**
     * Invoice controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

    private $slots = array();

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

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
            'controller' => 'com:nucleonplus.controller.slot'
        ));

        parent::_initialize($config);
    }

    /**
     * Create an entry to rewards system upon payment of invoice
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _afterEdit(KControllerContextInterface $context)
    {
        $entity = $context->result; // Invoice entity
        $order  = $this->getObject('com:nucleonplus.model.orders')->id($entity->order_id)->fetch();

        for ($i=0; $i < $order->package_slots; $i++)
        {
            $slot            = $this->createSlot($context);
            $this->slots[$i] = $slot;

            // Match succeeding slots to earlier (unpaid) slots
            $unpaidSlot = $this->getUnpaidSlot();

            // Make sure it's not matching to itself
            if ($unpaidSlot->id == $slot->id) {
                continue;
            }

            $this->allocateSlots($unpaidSlot, $slot);
        }
    }

    /**
     * Allocate slots
     *
     * @param KModelEntityRow $unpaidSlot
     * @param KModelEntityRow $slot
     *
     * @return void
     */
    private function allocateSlots(KModelEntityRow $unpaidSlot, KModelEntityRow $slot)
    {
        // Match the current slot to either left or right leg of the previous (unpaid) slot
        if ($unpaidSlot && is_null($unpaidSlot->lf_slot_id)) {
            $unpaidSlot->lf_slot_id = $slot->id;
            $unpaidSlot->save();

            $slot->consumed = 1;
            $slot->save();
        } elseif ($unpaidSlot && is_null($unpaidSlot->rt_slot_id)) {
            $unpaidSlot->rt_slot_id = $slot->id;
            $unpaidSlot->save();

            $slot->consumed = 1;
            $slot->save();
        }
    }

    /**
     * Get unpaid slot from set of slots
     *
     * @return KModelEntityRow
     */
    private function getUnpaidSlot()
    {
        foreach ($this->slots as $key => $slot) {
            if (is_null($slot->lf_slot_id) || is_null($slot->rt_slot_id)) {
                return $slot;
            }
        }
    }

    /**
     * Slot factory
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityRow
     */
    private function createSlot(KControllerContextInterface $context)
    {
        $controller = $this->getObject($this->_controller);
        $invoice    = $context->result; // Invoice entity

        $data = array(
            'account_number' => $invoice->customer_id
        );

        return $controller->add($data);
    }
}