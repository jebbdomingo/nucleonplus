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
     * The name of the column to use as the product column in the slot entry.
     *
     * @var string
     */
    protected $_product_column;

    /**
     * The name of the column to use as the account column in the slot entry.
     *
     * @var string
     */
    protected $_account_column;

    /**
     * Invoice controller identifier.
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
            'product_column' => array('product_id', 'id'),
            'account_column' => array('account_id', 'account_number'),
            'controller'     => 'com:nucleonplus.controller.slot',
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
    protected function _afterMarkpaid(KControllerContextInterface $context)
    {
        $entities = $context->result; // Order entity

        foreach ($entities as $entity)
        {
            // @todo this is no longer needed as the $context->result is the Order entity itself
            $order  = $this->getObject('com:nucleonplus.model.orders')->id($entity->id)->fetch();

            for ($i=0; $i < $order->package_slots; $i++)
            {
                $slot            = $this->createSlot($entity);
                $this->slots[$i] = $slot;
                $unpaidSlot      = $this->getOwnUnpaidSlot($this->slots);

                // Match succeeding slots to earlier (unpaid) slots
                if ($unpaidSlot->id == $slot->id) {
                    // Make sure it's not matching to itself
                    continue;
                }

                $this->placeOwnSlots($unpaidSlot, $slot);
            }

            $this->placeSlot($this->slots[0]);
        }
    }

    /**
     * Allocate member's own slots
     *
     * @param KModelEntityRow $unpaidSlot
     * @param KModelEntityRow $slot
     *
     * @return void
     */
    private function placeOwnSlots(KModelEntityRow $unpaidSlot, KModelEntityRow $slot)
    {
        // Match the current slot to either left or right leg of the previous (unpaid) slot
        if ($unpaidSlot && is_null($unpaidSlot->lf_slot_id)) {
            // Place to left leg of parent slot
            $unpaidSlot->lf_slot_id = $slot->id;
            $unpaidSlot->save();
            $slot->consumed = 1;
            $slot->save();
        } elseif ($unpaidSlot && is_null($unpaidSlot->rt_slot_id)) {
            // Place to right leg of parent slot
            $unpaidSlot->rt_slot_id = $slot->id;
            $unpaidSlot->save();
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
     * Place the member's first slot in the rewards system
     *
     * @param KModelEntityRow $firstSlot The member's first slot in his set of slots based on his product package purchase
     *
     * @return void
     */
    private function placeSlot(KModelEntityRow $firstSlot)
    {
        // All the slots from the rewards system
        if ($slots = $this->getObject('com:nucleonplus.model.slots')->account_id($firstSlot->account_id)->getUnpaidSlots())
        {
            $slots->{$slots->available_leg} = $firstSlot->id;
            $slots->save();
            $firstSlot->consumed = 1;
            $firstSlot->save();
            
            // Process rewards of an order
            // @todo move to dedicated rewards processing method
            $order = $this->getObject('com:nucleonplus.model.orders')->id($slots->product_id)->fetch();
            $order->processReward();
        }
    }

    /**
     * Slot factory
     *
     * @param KModelEntityRow $entity
     *
     * @return KModelEntityRow
     */
    private function createSlot(KModelEntityRow $entity)
    {
        $controller = $this->getObject($this->_controller);

        $data['product_id'] = $this->getProductData($entity);
        $data['account_id'] = $this->getAccountData($entity);

        return $controller->add($data);
    }

    /**
     * Get the product data based from the predefined set of columns
     *
     * @param KModelEntityInterface $object
     *
     * @return integer|string
     */
    private function getProductData(KModelEntityInterface $object)
    {
        if (is_array($this->_product_column))
        {
            foreach ($this->_product_column as $product)
            {
                if ($object->{$product})
                {
                    return $object->{$product};
                    break;
                }
            }
        }
        elseif ($object->{$this->_product_column}) return $object->{$this->_product_column};
        else return '#' . $object->id;
    }

    /**
     * Get the account data based from the predefined set of columns
     *
     * @param KModelEntityInterface $object
     *
     * @return integer|string
     */
    private function getAccountData(KModelEntityInterface $object)
    {
        if (is_array($this->_account_column))
        {
            foreach ($this->_account_column as $account)
            {
                if ($object->{$account})
                {
                    return $object->{$account};
                    break;
                }
            }
        }
        elseif ($object->{$this->_account_column}) return $object->{$this->_account_column};
        else return '#' . $object->id;
    }
}