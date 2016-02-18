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
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 *
 * The Package Rebate package requires the following data structure/model
 *
 * The Product or Item Entity should be Rewardable
 * should have a reference to an existing Reward
 * requires: reward_id fk column
 *
 * The Order Entity should be Rebatable
 * contains a reference to a Rewardable Product or Item
 * contains a reference to a Customer
 */
class ComNucleonplusRebatePackagereward extends KObject
{
    /**
     * The name of the column to use as the product column in the Rebate entity.
     *
     * @var string
     */
    protected $_product_column;

    /**
     * The name of the column to use as the account column in the Rebate entity.
     *
     * @var string
     */
    protected $_account_column;

    /**
     * Rebate controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Rebate default status.
     *
     * @param string
     */
    protected $_default_status;

    /**
     * Rebate active status.
     *
     * @param string
     */
    protected $_active_status;

    /**
     * Identifier of the Item model
     *
     * @var string
     */
    protected $_item_model;

    /**
     * The name of the Item's foreign key in the order's table
     *
     * @var string
     */
    protected $_item_fk_column;

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
    protected $_item_status;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller         = $config->controller;
        $this->_default_status     = $config->default_status;
        $this->_active_status      = $config->active_status;
        $this->_product_column     = KObjectConfig::unbox($config->product_column);
        $this->_account_column     = KObjectConfig::unbox($config->account_column);
        $this->_item_model         = $config->item_model;
        $this->_item_fk_column     = $config->item_fk_column;
        $this->_item_status_column = $config->item_status_column;
        $this->_item_status        = $config->item_status;
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
        $config->append([
            'controller'         => 'com:nucleonplus.controller.reward',
            'default_status'     => 'pending', // Default rebate status
            'active_status'      => 'active', // Active rebate status
            'product_column'     => ['id', 'product_id'], // ID of the Product or Item that is rewardable
            'account_column'     => ['account_id', 'account_number'], // ID of the customer in the order
            'item_model'         => 'com:nucleonplus.model.packages', // Rewardable Product or Item object's identifier
            'item_fk_column'     => 'package_id', // Product or Item's foreign key in the Order table
            'item_status_column' => 'invoice_status', // Order payment status column
            'item_status'        => 'paid', // The payment status of the Order to activate this rebate with
        ]);

        parent::_initialize($config);
    }

    /**
     * Create a Reward.
     *
     * @param KModelEntityInterface $object  The Order object
     */
    public function create(KModelEntityInterface $object)
    {
        $controller = $this->getObject($this->_controller);
        $item       = $this->getObject($this->_item_model)->id($object->{$this->_item_fk_column})->fetch();
     
        $data = array(
            'product_id'  => $this->_getProductData($object), // Item or Product ID
            'customer_id' => $this->_getAccountData($object), // Member's Account ID
            'status'      => $this->_default_status,
            'reward_id'   => $item->_rewardpackage_id,
            'slots'       => $item->_rewardpackage_slots,
            'prpv'        => $item->_rewardpackage_prpv,
            'drpv'        => $item->_rewardpackage_drpv,
            'irpv'        => $item->_rewardpackage_irpv
        );

        return $controller->add($data);
    }

    /**
     * Update a Reward.
     *
     * @param KModelEntityInterface $object The order object.
     *
     * @return void
     */
    public function updateStatus(KModelEntityInterface $object)
    {
        $controller = $this->getObject($this->_controller);

        if ($object->{$this->_item_status_column} == $this->_item_status)
        {
            $rebate = $this->getObject('com:nucleonplus.model.rewards')->product_id($object->id)->fetch();
            $rebate->status = $this->_active_status;
            $rebate->save();
        }
    }

    /**
     * Get the product data based from the predefined set of columns
     *
     * @param KModelEntityInterface $object
     *
     * @return integer|string
     */
    private function _getProductData(KModelEntityInterface $object)
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
    private function _getAccountData(KModelEntityInterface $object)
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