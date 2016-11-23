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
 * The Package MLM package requires the following data structure/model
 *
 * The Product or Item Entity should be Rewardable
 * should have a reference to an existing Reward
 * requires: reward_id fk column
 *
 * The Order Entity should be Rebatable
 * contains a reference to a Rewardable Product or Item
 * contains a reference to a Customer
 */
class ComNucleonplusMlmPackagereward extends KObject
{
    /**
     * The name of the column to use as the product column in the Reward entity.
     *
     * @var string
     */
    protected $_product_id_column;

    /**
     * The name of the column to use as the product name column in the Reward entity.
     *
     * @var string
     */
    protected $_product_name_column;

    /**
     * The name of the column to use as the account column in the Reward entity.
     *
     * @var string
     */
    protected $_account_id_column;

    /**
     * Reward controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Reward default status.
     *
     * @param string
     */
    protected $_default_status;

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
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller          = $config->controller;
        $this->_default_status      = $config->default_status;
        $this->_product_id_column   = KObjectConfig::unbox($config->product_id_column);
        $this->_product_name_column = KObjectConfig::unbox($config->product_name_column);
        $this->_account_id_column   = KObjectConfig::unbox($config->account_id_column);
        $this->_item_model          = $config->item_model;
        $this->_item_fk_column      = $config->item_fk_column;
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
            'controller'          => 'com:nucleonplus.controller.reward',
            'default_status'      => 'pending', // Default status
            'product_id_column'   => ['order_id'], // ID of the Product or Item that is rewardable
            'product_name_column' => ['package_name'], // Name of the Product or Item that is rewardable
            'account_id_column'   => ['account_id', 'account_number'], // ID of the customer in the order
            'item_model'          => 'com://admin/qbsync.model.items', // Rewardable Product or Item object's identifier
            'item_fk_column'      => 'ItemRef', // Product or Item's foreign key in the Order table
            'item_status'         => 'paid', // The payment status of the Order to activate this reward with
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
        $item = $this->getObject($this->_item_model)->ItemRef($object->{$this->_item_fk_column})->fetch();
        
        $data = array(
            'customer_id' => $this->_getAccountData($object), // Member's Account ID
            'product_id'  => $this->_getProductId($object),   // Item or Product ID
            'status'      => $this->_default_status,
            'slots'       => $item->slots,
            'prpv'        => $item->prpv,
            'drpv'        => $item->drpv,
            'irpv'        => $item->irpv,
            'rebates'     => $item->rebates,
            'charges'     => $item->charges,
            'profit'      => $item->profit,
            'type'        => $item->Type
        );
        
        $this->_actionCreate($data);
    }

    protected function _actionCreate($data)
    {
        $controller = $this->getObject($this->_controller);

        return $controller->add($data);
    }

    /**
     * Get the product data based from the predefined set of columns
     *
     * @param KModelEntityInterface $object
     *
     * @return integer|string
     */
    private function _getProductId(KModelEntityInterface $object)
    {
        if (is_array($this->_product_id_column))
        {
            foreach ($this->_product_id_column as $product_column)
            {
                if ($object->{$product_column})
                {
                    return $object->{$product_column};
                    break;
                }
            }
        }
        elseif ($object->{$this->_product_id_column}) return $object->{$this->_product_id_column};
        else return '#' . $object->id;
    }

    /**
     * Get the product's name based from the predefined set of columns
     *
     * @param KModelEntityInterface $object
     *
     * @return integer|string
     */
    private function _getProductName(KModelEntityInterface $object)
    {
        if (is_array($this->_product_name_column))
        {
            foreach ($this->_product_name_column as $product_column)
            {
                if ($object->{$product_column})
                {
                    return $object->{$product_column};
                    break;
                }
            }
        }
        elseif ($object->{$this->_product_name_column}) return $object->{$this->_product_name_column};
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
        if (is_array($this->_account_id_column))
        {
            foreach ($this->_account_id_column as $account)
            {
                if ($object->{$account})
                {
                    return $object->{$account};
                    break;
                }
            }
        }
        elseif ($object->{$this->_account_id_column}) return $object->{$this->_account_id_column};
        else return '#' . $object->id;
    }
}