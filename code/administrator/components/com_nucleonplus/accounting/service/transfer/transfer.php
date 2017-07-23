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
class ComNucleonplusAccountingServiceTransfer extends KObject implements ComNucleonplusAccountingServiceTransferInterface
{
    /**
     * Is queue
     *
     * @var boolean
     */
    protected $_queue = false;

    /**
     * Is disabled
     *
     * @var boolean
     */
    protected $_disabled = false;

    /**
     *
     * @var ComKoowaControllerModel
     */
    protected $_controller;

    /**
     *
     * @var ComQbsyncServiceTransfer
     */
    protected $_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $this->getObject($config->transfer_controller);
        $this->_service    = $this->getObject($config->transfer_service);

        // Accounts
        $this->_online_payments_account           = $config->online_payments_account;
        $this->_savings_account                   = $config->savings_account;
        $this->_checking_account                  = $config->checking_account;
        $this->_charges_account                   = $config->charges_account;
        $this->_rebates_account                   = $config->rebates_account;
        $this->_dr_bonus_account                  = $config->dr_bonus_account;
        $this->_patronage_account                 = $config->patronage_account;
        $this->_unilevel_dr_bonus_account         = $config->unilevel_dr_bonus_account;
        $this->_unilevel_ir_bonus_account         = $config->unilevel_ir_bonus_account;
        $this->_surplus_patronage_account         = $config->surplus_patronage_account;
        $this->_surplus_unilevel_dr_bonus_account = $config->surplus_unilevel_dr_bonus_account;
        $this->_surplus_unilevel_ir_bonus_account = $config->surplus_unilevel_ir_bonus_account;
        $this->_delivery_expense_account          = $config->delivery_expense_account;
        $this->_revenue_account                   = $config->revenue_account;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options
     * 
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $data = $this->getObject('com:nucleonplus.accounting.service.data');

        $config->append(array(
            'transfer_service'                  => 'com:qbsync.service.transfer',
            'transfer_controller'               => 'com:qbsync.controller.transfer',
            'online_payments_account'           => $data->ACCOUNT_ONLINE_PAYMENTS,
            'savings_account'                   => $data->ACCOUNT_BANK_REF,
            'checking_account'                  => $data->ACCOUNT_CHECKING_REF,
            'charges_account'                   => $data->ACCOUNT_CHARGES,
            'rebates_account'                   => $data->ACCOUNT_REBATES,
            'dr_bonus_account'                  => $data->ACCOUNT_DIRECT_REFERRAL_BONUS,
            'patronage_account'                 => $data->ACCOUNT_PATRONAGE,
            'unilevel_dr_bonus_account'         => $data->ACCOUNT_REFERRAL_DIRECT,
            'unilevel_ir_bonus_account'         => $data->ACCOUNT_REFERRAL_INDIRECT,
            'surplus_patronage_account'         => $data->ACCOUNT_PATRONAGE_FLUSHOUT,
            'surplus_unilevel_dr_bonus_account' => $data->ACCOUNT_REFERRAL_DIRECT_FLUSHOUT,
            'surplus_unilevel_ir_bonus_account' => $data->ACCOUNT_REFERRAL_INDIRECT_FLUSHOUT,
            'delivery_expense_account'          => $data->ACCOUNT_EXPENSE_DELIVERY,
            'revenue_account'                   => $data->ACCOUNT_REVENUE,
        ));

        parent::_initialize($config);
    }

    /**
     * Allocate outright revenue
     * 
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateRevenue($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_revenue_account;
        $note          = 'Outright revenue from product';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * Record online payment remittance
     * 
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function depositOnlinePayment($entityId, $amount)
    {
        $sourceAccount = $this->_online_payments_account;
        $targetAccount = $this->_savings_account;
        $note          = 'Deposit from online payment processing network';

        return $this->_transfer('payout', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateRebates($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_rebates_account;
        $note          = 'Rebates';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateDirectReferral($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_dr_bonus_account;
        $note          = 'Direct Referral Bonus';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocatePatronage($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_patronage_account;
        $note          = 'Patronage';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateSurplusPatronage($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_surplus_patronage_account;
        $note          = 'Flushout Patronage i.e. a slot that doesn\'t have available slot to connect with';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateDRBonus($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_unilevel_dr_bonus_account;
        $note          = 'Unilevel Direct Referral';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateIRBonus($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_unilevel_ir_bonus_account;
        $note          = 'Unilevel Indirect Referral';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateSurplusDRBonus($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_surplus_unilevel_dr_bonus_account;
        $note          = 'Flushout Unilevel Direct Referral i.e. an account that doesn\'t have a referrer';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateSurplusIRBonus($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_surplus_unilevel_ir_bonus_account;
        $note          = 'Flushout Unilevel Indirect Referral i.e. an account that doesn\'t have an indirect referrer';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateCharges($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_charges_account;
        $note          = 'Charges';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateDeliveryExpense($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_delivery_expense_account;
        $note          = 'Delivery Expense';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function rebatesCheck($entityId, $amount)
    {
        $sourceAccount = $this->_rebates_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Rebates Check';

        $transfer = $this->_transfer('payout', $entityId, $sourceAccount, $targetAccount, $amount, $note);

        // Try to sync
        if ($transfer->sync() == false)
        {
            $error = $transfer->getStatusMessage();
            throw new KControllerExceptionActionFailed($error ? $error : "Sync Error: Transfer #{$transfer->id}");
        }

        return $transfer;
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function directReferralBonusCheck($entityId, $amount)
    {
        $sourceAccount = $this->_dr_bonus_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Direct Referral Bonus Check';

        $transfer = $this->_transfer('payout', $entityId, $sourceAccount, $targetAccount, $amount, $note);

        // Try to sync
        if ($transfer->sync() == false)
        {
            $error = $transfer->getStatusMessage();
            throw new KControllerExceptionActionFailed($error ? $error : "Sync Error: Transfer #{$transfer->id}");
        }

        return $transfer;
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function commissionCheck($entityId, $amount)
    {
        $sourceAccount = $this->_patronage_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Patronage Bonus Check';

        $transfer = $this->_transfer('payout', $entityId, $sourceAccount, $targetAccount, $amount, $note);
        
        // Try to sync
        if ($transfer->sync() == false)
        {
            $error = $transfer->getStatusMessage();
            throw new KControllerExceptionActionFailed($error ? $error : "Sync Error: Transfer #{$transfer->id}");
        }

        return $transfer;
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function directReferralCheck($entityId, $amount)
    {
        $sourceAccount = $this->_unilevel_dr_bonus_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Unilevel Direct Referral Check';

        $transfer = $this->_transfer('payout', $entityId, $sourceAccount, $targetAccount, $amount, $note);

        // Try to sync
        if ($transfer->sync() == false)
        {
            $error = $transfer->getStatusMessage();
            throw new KControllerExceptionActionFailed($error ? $error : "Sync Error: Transfer #{$transfer->id}");
        }

        return $transfer;
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function indirectReferralCheck($entityId, $amount)
    {
        $sourceAccount = $this->_unilevel_ir_bonus_account;
        $targetAccount = $this->_checking_account;
        $note          = 'Unilevel Indirect Referral Check';

        $transfer = $this->_transfer('payout', $entityId, $sourceAccount, $targetAccount, $amount, $note);
        
        // Try to sync
        if ($transfer->sync() == false)
        {
            $error = $transfer->getStatusMessage();
            throw new KControllerExceptionActionFailed($error ? $error : "Sync Error: Transfer #{$transfer->id}");
        }

        return $transfer;
    }

    /**
     * Transfer funds
     * 
     * @param string  $entity
     * @param integer $entityId
     * @param integer $fromAccount
     * @param integer $toAccount
     * @param decimal $amount
     * @param string  $note [optional]
     *
     * @throws Exception API error
     *
     * @return KModelEntityInterface|string
     */
    protected function _transfer($entity, $entityId, $fromAccount, $toAccount, $amount, $note = null)
    {
        if ($this->_disabled) {
            return false;
        }

        if ($this->_queue)
        {
            return $this->_controller->add(array(
                'entity'         => $entity,
                'entity_id'      => $entityId,
                'FromAccountRef' => $fromAccount,
                'ToAccountRef'   => $toAccount,
                'Amount'         => $amount,
                'TxnDate'        => date('Y-m-d'),
                'PrivateNote'    => "{$entityId}_{$note}"
            ));
        }
        else
        {
            return $this->_service->create(array(
                'FromAccountRef' => $fromAccount,
                'ToAccountRef'   => $toAccount,
                'Amount'         => $amount,
                'TxnDate'        => date('Y-m-d'),
                'PrivateNote'    => "{$entityId}_{$note}"
            ));
        }
    }
}