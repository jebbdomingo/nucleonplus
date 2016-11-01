<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * @todo Implement a local queue of accounting/inventory transactions in case of trouble connecting to accounting system
 */
class ComNucleonplusAccountingServiceSalesreceipt extends KObject implements ComNucleonplusAccountingServiceSalesreceiptInterface
{
    protected $_disabled = false;
    
    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_salesreceipt;

    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_salesreceipt_line;

    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_item_controller;

    /**
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_transfer_service;

    /**
     *
     * @var integer
     */
    protected $_department_ref;

    /**
     *
     * @var integer
     */
    protected $_bank_account_ref;

    /**
     *
     * @var integer
     */
    protected $_undeposited_account_ref;

    /**
     *
     * @var integer
     */
    protected $_shipping_account;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_salesreceipt            = $this->getObject($config->salesreceipt_controller);
        $this->_salesreceipt_line       = $this->getObject($config->salesreceipt_line_controller);
        $this->_item_controller         = $this->getObject($config->item_controller);
        $this->_department_ref          = $config->department_ref;
        $this->_bank_account_ref        = $config->bank_account_ref;
        $this->_undeposited_account_ref = $config->undeposited_account_ref;
        $this->_shipping_account        = $config->shipping_account;

        // Transfer service
        $identifier = $this->getIdentifier($config->transfer_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceTransferInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceTransferInterface"
            );
        }
        else $this->_transfer_service = $service;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $data = $this->getObject('com:nucleonplus.accounting.service.data');

        $config->append(array(
            'salesreceipt_controller'      => 'com:qbsync.controller.salesreceipt',
            'salesreceipt_line_controller' => 'com:qbsync.controller.salesreceiptline',
            'item_controller'              => 'com:qbsync.controller.item',
            'transfer_service'             => 'com:nucleonplus.accounting.service.transfer',
            'department_ref'               => $data->store_angono,
            'bank_account_ref'             => $data->account_bank_ref, // Bank Account
            'undeposited_account_ref'      => $data->account_undeposited_ref, // Undeposited Funds Account
            'shipping_account'             => $data->ACCOUNT_INCOME_SHIPPING
        ));

        parent::_initialize($config);
    }

    /**
     * Record sales transaction in the accounting system 
     *
     * @param KModelEntityInterface $order
     *
     * @return void
     */
    public function recordSale(KModelEntityInterface $order)
    {
        if ($this->_disabled) {
            return false;
        }

        // Create sales receipt sync queue
        $salesReceiptData = array(
            'DocNumber'    => $order->id,
            'TxnDate'      => date('Y-m-d'),
            'CustomerRef'  => $order->_account_customer_ref,
            'CustomerMemo' => 'Thank you for your business and have a great day!',
        );

        if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        {
            // Online payment
            $salesReceiptData['DepartmentRef']       = $this->_department_ref; // Angono EC Valle store
            $salesReceiptData['DepositToAccountRef'] = $this->_bank_account_ref; // Bank Account
            $salesReceiptData['transaction_type']    = 'online'; // Customer ordered thru website
        }
        else
        {
            // Cash
            $user     = $this->getObject('user');
            $employee = $this->getObject('com:nucleonplus.model.employeeaccounts')->user_id($user->getId())->fetch();
            
            $salesReceiptData['DepartmentRef']       = $employee->DepartmentRef; // Store branch
            $salesReceiptData['DepositToAccountRef'] = $this->_undeposited_account_ref; // Undeposited Funds Account
            $salesReceiptData['transaction_type']    = 'offline'; // Order placed via onsite POS
        }

        $salesReceipt = $this->_salesreceipt->add($salesReceiptData);
        $itemsQty     = array();

        // Product line items
        foreach ($order->getOrderItems() as $orderItem)
        {
            $item = $this->getObject('com://admin/qbsync.model.items')->ItemRef($orderItem->ItemRef)->fetch();
            
            // Business Package/Bundle
            if ($item->Type == ComQbsyncModelEntityItem::TYPE_GROUP)
            {
                // Update item quantity
                $groupedItems = $this->getObject('com://admin/qbsync.model.itemgroups')->parent_id($item->ItemRef)->fetch();
                $quantity     = 0;

                foreach ($groupedItems as $groupedItem)
                {
                    if ($groupedItem->_item_type == 'Inventory')
                    {
                        $quantity += ((int) $orderItem->quantity * (int) $groupedItem->quantity);
                        $oItem    = $this->getObject('com://admin/qbsync.model.items')->ItemRef($groupedItem->ItemRef)->fetch();
                        @$itemsQty[$oItem->ItemRef] = $quantity;
                    }
                }

                // Add salesreceipt line
                $quantity = (int) $orderItem->quantity;
                $this->_addSalesReceiptLine(
                    $salesReceipt->id,
                    $item->Name,
                    $item->ItemRef,
                    $quantity,
                    0,
                    $item->Type
                );
            }
            // Retail Item
            else
            {
                // Add salesreceipt line
                $quantity = (int) $orderItem->quantity;
                $this->_addSalesReceiptLine(
                    $salesReceipt->id,
                    $item->Name,
                    $item->ItemRef,
                    $quantity,
                    ($item->UnitPrice * $quantity),
                    $item->Type
                );

                @$itemsQty[$item->ItemRef] += $quantity;
            }
        }

        $this->_updateQuantity($itemsQty);

        // Service line items
        if ($order->shipping_method == 'xend' && $order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY)
        {
            // Delivery charge
            if ($shippingCost = $order->shipping_cost)
            {
                $this->_salesreceipt_line->add(array(
                    'SalesReceipt' => $salesReceipt->id,
                    'Description'  => "Shipping",
                    'ItemRef'      => $this->_shipping_account,
                    'Amount'       => $shippingCost
                ));
            }
        }

        // Allocation parts of sale
        foreach ($order->getRewards() as $reward)
        {
            if ($reward->type == ComNucleonplusModelEntityReward::REWARD_PACKAGE) {
                $charges = $reward->charges * $reward->slots;
            }
            else $charges = $reward->charges;

            $this->_transfer_service->allocateCharges($order->id, $charges);
        }
    }

    protected function _addSalesReceiptLine($salesReceiptId, $description, $ItemRef, $quantity, $amount, $type = ComQbsyncModelEntityItem::TYPE_INVENTORY_ITEM)
    {
        $this->_salesreceipt_line->add(array(
            'SalesReceipt' => $salesReceiptId,
            'Description'  => $description,
            'ItemRef'      => $ItemRef,
            'Qty'          => $quantity,
            'Amount'       => $amount,
            'Type'         => $type
        ));
    }

    /**
     * Update item's quantity purchased for real time inventory quantity tracking
     *
     * @param array $itemsQty
     *
     * @return KModelEntityInterface
     */
    protected function _updateQuantity($itemsQty)
    {
        foreach ($itemsQty as $ItemRef => $quantity)
        {
            $entity = $this->getObject('com://admin/qbsync.model.items')->ItemRef($ItemRef)->fetch();

            if (count($entity))
            {
                $entity->updateQuantityPurchased($quantity);
                $entity->save();
            }
        }

        return $entity;
    }
}