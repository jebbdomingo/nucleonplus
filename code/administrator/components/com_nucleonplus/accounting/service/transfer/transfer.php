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
     *
     * @var ComKoowaControllerModel
     */
    protected $_transfer_controller;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_transfer_controller = $this->getObject($config->transfer_controller);

        // Accounts
        $this->_savings_account                   = $config->savings_account;
        $this->_checking_account                  = $config->checking_account;
        $this->_system_fee_account                = $config->system_fee_account;
        $this->_contingency_fund_account          = $config->contingency_fund_account;
        $this->_operating_expense_budget_account  = $config->operating_expense_budget_account;
        $this->_dr_bonus_account                  = $config->dr_bonus_account;
        $this->_patronage_account                 = $config->patronage_account;
        $this->_unilevel_dr_bonus_account         = $config->unilevel_dr_bonus_account;
        $this->_unilevel_ir_bonus_account         = $config->unilevel_ir_bonus_account;
        $this->_surplus_patronage_account         = $config->surplus_patronage_account;
        $this->_surplus_unilevel_dr_bonus_account = $config->surplus_unilevel_dr_bonus_account;
        $this->_surplus_unilevel_ir_bonus_account = $config->surplus_unilevel_ir_bonus_account;
        $this->_delivery_expense_account          = $config->delivery_expense_account;
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
            'transfer_controller'               => 'com:qbsync.controller.transfer',
            'savings_account'                   => $data->ACCOUNT_BANK_REF,
            'checking_account'                  => $data->ACCOUNT_CHECKING_REF,
            'system_fee_account'                => $data->ACCOUNT_SYSTEM_FEE,
            'contingency_fund_account'          => $data->ACCOUNT_CONTINGENCY_FUND,
            'operating_expense_budget_account'  => $data->ACCOUNT_EXPENSE_OPERATING,
            'dr_bonus_account'                  => $data->ACCOUNT_DIRECT_REFERRAL_BONUS,
            'patronage_account'                 => $data->ACCOUNT_PATRONAGE,
            'unilevel_dr_bonus_account'         => $data->ACCOUNT_REFERRAL_DIRECT,
            'unilevel_ir_bonus_account'         => $data->ACCOUNT_REFERRAL_INDIRECT,
            'surplus_patronage_account'         => $data->ACCOUNT_PATRONAGE_FLUSHOUT,
            'surplus_unilevel_dr_bonus_account' => $data->ACCOUNT_REFERRAL_DIRECT_FLUSHOUT,
            'surplus_unilevel_ir_bonus_account' => $data->ACCOUNT_REFERRAL_INDIRECT_FLUSHOUT,
            'delivery_expense_account'          => $data->ACCOUNT_EXPENSE_DELIVERY,
        ));

        parent::_initialize($config);
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
        $note          = 'Commission';

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
        $note          = 'Flushout Commission i.e. a slot that doesn\'t have available slot to connect with';

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
        $note          = 'Direct Referral';

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
        $note          = 'Indirect Referral';

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
        $note          = 'Flushout Direct Referral i.e. an account that doesn\'t have a referrer';

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
        $note          = 'Flushout Indirect Referral i.e. an account that doesn\'t have an indirect referrer';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateSystemFee($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_system_fee_account;
        $note          = 'System Fee';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateContingencyFund($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_contingency_fund_account;
        $note          = 'Contingency Fund';

        return $this->_transfer('order', $entityId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $entityId
     * @param decimal $amount
     *
     * @return KModelEntityInterface
     */
    public function allocateOperationsFund($entityId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_operating_expense_budget_account;
        $note          = 'Operating Expense';

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
     * @return KModelEntityInterface
     */
    protected function _transfer($entity, $entityId, $fromAccount, $toAccount, $amount, $note = null)
    {
        return $this->_transfer_controller->add(array(
             'entity'         => $entity,
             'entity_id'      => $entityId,
             'FromAccountRef' => $fromAccount,
             'ToAccountRef'   => $toAccount,
             'Amount'         => $amount,
             'PrivateNote'    => "{$entityId}_{$note}",
        ));
    }
}