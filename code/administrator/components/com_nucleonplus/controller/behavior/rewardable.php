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
            'product_column' => array('id', 'product_id'),
            'account_column' => array('account_id', 'account_number'),
            'controller'     => 'com:nucleonplus.controller.slot',
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
            $reward  = $this->createRebate($order, $package);

            // Create and organize member's own set of slots
            $slot = $this->createOwnSlots($reward, $package->slots);

            // Connect the member's primary slot to an available slot of other members in the rewards sytem
            $this->connectToOtherSlot($slot);
        }
    }

    /**
     * Create a Member' Rebate entity
     *
     * @param KModelEntityRow    $order
     * @param KModelEntityRowset $package
     *
     * @return com:nucleonplus.model.rebates
     */
    private function createRebate(KModelEntityRow $order, KModelEntityRowset $package)
    {
        $controller = $this->getObject('com:nucleonplus.controller.rebate');

        $data = array(
            'product_id'  => $this->getProductData($order), // Order ID
            'customer_id' => $this->getAccountData($order), // Member's Account ID
            'reward_id'   => $package->reward_id,
            'slots'       => $package->slots,
            'prpv'        => $package->prpv,
            'drpv'        => $package->drpv,
            'irpv'        => $package->irpv
        );

        return $controller->add($data);
    }

    /**
     * Create and organize own slots
     *
     * @param KModelEntityRow $reward
     * @param integer         $num_slots
     *
     * @return KModelEntityRow Primary Slot
     */
    private function createOwnSlots($reward, $num_slots)
    {
        $slots = array();

        for ($i=0; $i < $num_slots; $i++)
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
            foreach ($this->_product_column as $product_column)
            {
                if ($object->{$product_column})
                {
                    return $object->{$product_column};
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

    /**
     * Slot factory
     *
     * @param KModelEntityRow $reward
     *
     * @return KModelEntityRow
     */
    private function createSlot(KModelEntityRow $reward)
    {
        $controller = $this->getObject($this->_controller);

        $data['rebate_id'] = $reward->id;

        return $controller->add($data);
    }
}