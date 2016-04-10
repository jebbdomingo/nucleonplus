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
        $config->append(array(
            'transfer_controller'                    => 'com:qbsync.controller.transfer',
            'savings_account'                        => 269,
            'system_fee_account'                     => 291,
            'contingency_fund_account'               => 296,
            'operating_expense_budget_account'       => 292,
            'rebates_account'                        => 288,
            'directreferral_bonus_account'           => 289,
            'indirectreferral_bonus_account'         => 290,
            'surplusrebates_account'                 => 295,
            'surplus_directreferral_bonus_account'   => 293,
            'surplus_indirectreferral_bonus_account' => 294,
            'delivery_expense_account'               => 297,
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
        $note          = 'Transfer part of sale to rebates asset account';

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
        $note          = 'Transfer surplus rebates i.e. a slot that doesn\'t have available slot to connect with';

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
        $note          = 'Transfer part of sale to direct referral incentives asset account';

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
        $note          = 'Transfer part of sale to indirect referral incentives asset account';

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
        $note          = 'Transfer surplus direct referral bonus i.e. an account that doesn\'t have a referrer';

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
        $note          = 'Transfer surplus indirect referral bonus i.e. an account that doesn\'t have an indirect referrer';

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
        $note          = 'Transfer part of sale to system fee asset account';

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
        $note          = 'Transfer part of sale to contingency fund asset account';

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
        $note          = 'Transfer part of sale to operating budget asset account';

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
        $note          = 'Transfer part of sale to delivery expense allocation asset account';

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