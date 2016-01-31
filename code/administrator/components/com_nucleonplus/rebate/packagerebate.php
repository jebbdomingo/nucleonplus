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
 */
class ComNucleonplusRebatePackagerebate extends KObject
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
     * The name of the Item's foreign key in the order's table
     *
     * @var string
     */
    protected $_item_fk_column;

    /**
     * Identifier of the Item model
     *
     * @var string
     */
    protected $_item_model;

    /**
     * Rebate controller identifier.
     *
     * @param string|KObjectIdentifierInterface
     */
    protected $_controller;

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
        $this->_item_fk_column = $config->item_fk_column;
        $this->_item_model     = $config->item_model;
        $this->_controller     = $config->controller;
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
            'controller'     => 'com:nucleonplus.controller.rebate',
            'product_column' => ['id', 'product_id'],
            'account_column' => ['account_id', 'account_number'],
            'item_fk_column' => 'package_id', // Product or Item's foreign key in Order table
            'item_model'     => 'com:nucleonplus.model.packages', // Product or Item object's identifier
        ]);

        parent::_initialize($config);
    }

    /**
     * Create a Rebate.
     *
     * @param KModelEntityInterface $object  The activity object on which the action is performed.
     */
    public function create(KModelEntityInterface $object)
    {
        $itemModel = $this->getObject($this->_item_model);

        if ($itemModel->hasBehavior('rewardable'))
        {
            $controller = $this->getObject($this->_controller);
            $item       = $itemModel->id($object->{$this->_item_fk_column})->fetch();
         
            $data = array(
                'product_id'  => $this->getProductData($object), // Order ID
                'customer_id' => $this->getAccountData($object), // Member's Account ID
                'reward_id'   => $item->reward_id,
                'slots'       => $item->slots,
                'prpv'        => $item->prpv,
                'drpv'        => $item->drpv,
                'irpv'        => $item->irpv
            );

            return $controller->add($data);
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
}