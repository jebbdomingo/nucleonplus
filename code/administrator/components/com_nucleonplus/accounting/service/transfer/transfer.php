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
        $this->_savings_account                        = $config->savings_account;
        $this->_system_fee_account                     = $config->system_fee_account;
        $this->_contingency_fund_account               = $config->contingency_fund_account;
        $this->_operating_expense_budget_account       = $config->operating_expense_budget_account;
        $this->_rebates_account                        = $config->rebates_account;
        $this->_directreferral_bonus_account           = $config->directreferral_bonus_account;
        $this->_indirectreferral_bonus_account         = $config->indirectreferral_bonus_account;
        $this->_surplusrebates_account                 = $config->surplusrebates_account;
        $this->_surplus_directreferral_bonus_account   = $config->surplus_directreferral_bonus_account;
        $this->_surplus_indirectreferral_bonus_account = $config->surplus_indirectreferral_bonus_account;
        $this->_delivery_expense_account               = $config->delivery_expense_account;
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
            'transfer_controller'                    => 'com:qbsync.controller.transfer',
            'savings_account'                        => $data->ACCOUNT_SAVINGS,
            'system_fee_account'                     => $data->ACCOUNT_SYSTEM_FEE,
            'contingency_fund_account'               => $data->ACCOUNT_CONTINGENCY_FUND,
            'operating_expense_budget_account'       => $data->ACCOUNT_EXPENSE_OPERATING,
            'rebates_account'                        => $data->ACCOUNT_REBATES,
            'directreferral_bonus_account'           => $data->ACCOUNT_REFERRAL_DIRECT,
            'indirectreferral_bonus_account'         => $data->ACCOUNT_REFERRAL_INDIRECT,
            'surplusrebates_account'                 => $data->ACCOUNT_REBATES_FLUSHOUT,
            'surplus_directreferral_bonus_account'   => $data->ACCOUNT_REFERRAL_DIRECT_FLUSHOUT,
            'surplus_indirectreferral_bonus_account' => $data->ACCOUNT_REFERRAL_INDIRECT_FLUSHOUT,
            'delivery_expense_account'               => $data->ACCOUNT_EXPENSE_DELIVERY,
        ));

        parent::_initialize($config);
    }

    /**
     *
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateRebates($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_rebates_account;
        $note          = 'Rebates';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateSurplusRebates($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_surplusrebates_account;
        $note          = 'Surplus Rebates i.e. a slot that doesn\'t have available slot to connect with';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateDRBonus($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_directreferral_bonus_account;
        $note          = 'Direct Referral';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateIRBonus($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_indirectreferral_bonus_account;
        $note          = 'Indirect Referral';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateSurplusDRBonus($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_surplus_directreferral_bonus_account;
        $note          = 'Flushout Direct Referral i.e. an account that doesn\'t have a referrer';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateSurplusIRBonus($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_surplus_indirectreferral_bonus_account;
        $note          = 'Flushout Indirect Referral i.e. an account that doesn\'t have an indirect referrer';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateSystemFee($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_system_fee_account;
        $note          = 'System Fee';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateContingencyFund($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_contingency_fund_account;
        $note          = 'Contingency Fund';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateOperationsFund($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_operating_expense_budget_account;
        $note          = 'Operating Expense';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * @param integer $orderId
     * @param decimal $amount
     *
     * @return void
     */
    public function allocateDeliveryExpense($orderId, $amount)
    {
        $sourceAccount = $this->_savings_account;
        $targetAccount = $this->_delivery_expense_account;
        $note          = 'Delivery Expense';

        return $this->_transfer($orderId, $sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * Transfer funds
     * 
     * @param integer $orderId
     * @param integer $fromAccount
     * @param integer $toAccount
     * @param decimal $amount
     * @param string  $note [optional]
     *
     * @throws Exception API error
     *
     * @return resource
     */
    protected function _transfer($orderId, $fromAccount, $toAccount, $amount, $note = null)
    {
        return $this->_transfer_controller->add(array(
             'order_id'       => $orderId,
             'FromAccountRef' => $fromAccount,
             'ToAccountRef'   => $toAccount,
             'Amount'         => $amount,
             'PrivateNote'    => "{$orderId}_{$note}",
        ));
    }
}