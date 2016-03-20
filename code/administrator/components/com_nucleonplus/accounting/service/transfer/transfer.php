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
class ComNucleonplusAccountingServiceTransfer extends ComNucleonplusAccountingServiceObject implements ComNucleonplusAccountingServiceTransferInterface
{
    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Accounts
        $this->_undeposited_funds_account              = $config->undeposited_funds_account;
        $this->_system_fee_account                     = $config->system_fee_account;
        $this->_contingency_fund_account               = $config->contingency_fund_account;
        $this->_operating_expense_budget_account       = $config->operating_expense_budget_account;
        $this->_rebates_account                        = $config->rebates_account;
        $this->_directreferral_bonus_account           = $config->directreferral_bonus_account;
        $this->_indirectreferral_bonus_account         = $config->indirectreferral_bonus_account;
        $this->_surplusrebates_account                 = $config->surplusrebates_account;
        $this->_surplus_directreferral_bonus_account   = $config->surplus_directreferral_bonus_account;
        $this->_surplus_indirectreferral_bonus_account = $config->surplus_indirectreferral_bonus_account;
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
            'undeposited_funds_account'              => 92,
            'system_fee_account'                     => 138,
            'contingency_fund_account'               => 139,
            'operating_expense_budget_account'       => 140,
            'rebates_account'                        => 141,
            'directreferral_bonus_account'           => 142,
            'indirectreferral_bonus_account'         => 145,
            'surplusrebates_account'                 => 144,
            'surplus_directreferral_bonus_account'   => 146,
            'surplus_indirectreferral_bonus_account' => 147,
        ));

        parent::_initialize($config);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateRebates($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_rebates_account;
        $note          = 'Transfer part of sale to rebates asset account';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateSurplusRebates($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_surplusrebates_account;
        $note          = 'Transfer surplus rebates i.e. a slot that doesn\'t have available slot to connect with';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateDRBonus($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_directreferral_bonus_account;
        $note          = 'Transfer part of sale to direct referral incentives asset account';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateIRBonus($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_indirectreferral_bonus_account;
        $note          = 'Transfer part of sale to indirect referral incentives asset account';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateSurplusDRBonus($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_surplus_directreferral_bonus_account;
        $note          = 'Transfer surplus direct referral bonus i.e. an account that doesn\'t have a referrer';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateSurplusIRBonus($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_surplus_indirectreferral_bonus_account;
        $note          = 'Transfer surplus indirect referral bonus i.e. an account that doesn\'t have an indirect referrer';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateSystemFee($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_system_fee_account;
        $note          = 'Transfer part of sale to system fee asset account';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateContingencyFund($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_contingency_fund_account;
        $note          = 'Transfer part of sale to contingency fund asset account';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     *
     * @param decimal $order
     *
     * @return void
     */
    public function allocateOperationsFund($amount)
    {
        $sourceAccount = $this->_undeposited_funds_account;
        $targetAccount = $this->_operating_expense_budget_account;
        $note          = 'Transfer part of sale to operating budget asset account';

        return $this->_transfer($sourceAccount, $targetAccount, $amount, $note);
    }

    /**
     * Transfer funds
     *
     * @param integer $fromAccount
     * @param integer $toAccount
     * @param decimal $amount
     * @param string  $note [optional]
     *
     * @throws Exception API error
     *
     * @return resource
     */
    protected function _transfer($fromAccount, $toAccount, $amount, $note = null)
    {
        $Transfer = new QuickBooks_IPP_Object_Transfer();
        $Transfer->setFromAccountRef($fromAccount);
        $Transfer->setToAccountRef($toAccount);
        $Transfer->setAmount($amount);
        $Transfer->setPrivateNote($note);

        $TransferService = new QuickBooks_IPP_Service_Transfer();

        if ($resp = $TransferService->add($this->Context, $this->realm, $Transfer)) {
            return $resp;
        }
        else throw new Exception($TransferService->lastError($this->Context));
    }
}