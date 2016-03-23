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
     * The payment status column of the Item or Order
     *
     * @var string
     */
    protected $_item_status_column;

    /**
     * The payment status of the Item or Order
     *
     * @var string
     */
    protected $_item_paid_status;

    /**
     * Accounting Service
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_accounting_service;

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
        $this->_item_status_column   = $config->item_status_column;
        $this->_item_paid_status     = $config->item_paid_status;

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
            'reward_active_status' => 'active', // Reward's active status
            'item_model'           => 'com:nucleonplus.model.packages', // Product or Item object's identifier
            'item_status_column'   => 'invoice_status', // Order or Item's payment status column
            'item_paid_status'     => 'paid', // The payment status of the Order to activate this rebate with
            'accounting_service'   => 'com:nucleonplus.accounting.service.transfer'
        ));

        parent::_initialize($config);
    }

    /**
     * Create corresponding slots in the Rewards system
     *
     * @param KModelEntityInterface $order Order entity
     *
     * @return void
     */
    public function create(KModelEntityInterface $order)
    {
        $reward = $this->getObject($this->_reward_model)->product_id($order->id)->fetch();

        // Create the slots only if the order/item is paid and the reward is not yet activated
        if (($order->{$this->_item_status_column} == $this->_item_paid_status)
            && ($reward->status <> $this->_reward_active_status))
        {
            $reward->status = $this->_reward_active_status;
            $reward->save();

            // Create and organize member's own set of slots
            $slot = $this->createOwnSlots($order, $reward->slots);

            // Connect the member's primary slot to an available slot of other members in the rewards sytem
            $this->connectToOtherSlot($slot);
        }
        else throw new KControllerExceptionRequestInvalid('Invalid Request');
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

        $data['reward_id'] = $order->_reward_id;

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
        $slotReward = $slot->getReward();

        // All the slots from the rewards system
        if ($unpaidSlot = $this->getObject($this->_model)->reward_id($slot->reward_id)->getUnpaidSlots())
        {
            $unpaidSlot->{$unpaidSlot->available_leg} = $slot->id;
            $unpaidSlot->save();
            $slot->consume();

            $this->_accounting_service->allocateRebates($slotReward->product_id, $slotReward->prpv);
            
            // Process member rebates
            // TODO move to dedicated rewards processing method
            $reward = $this->getObject($this->_reward_model)->id($unpaidSlot->reward_id)->fetch();
            $reward->processRebate();
        }
        else
        {
            // Transfer surplus rebates i.e. a slot that doesn't have available slot to connect with
            $this->_accounting_service->allocateSurplusRebates($slotReward->product_id, $slotReward->prpv);
        }
    }
}