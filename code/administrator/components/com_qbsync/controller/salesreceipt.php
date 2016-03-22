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
 * Account Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComQbsyncControllerSalesreceipt extends ComKoowaControllerModel
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
        $this->_undeposited_funds_account        = $config->undeposited_funds_account;
        $this->_sales_of_product_account         = $config->sales_of_product_account;
        $this->_system_fee_account               = $config->system_fee_account;
        $this->_contingency_fund_account         = $config->contingency_fund_account;
        $this->_operating_expense_budget_account = $config->operating_expense_budget_account;
        $this->_sales_account                    = $config->sales_account;
        $this->_system_fee_rate                  = $config->system_fee_rate;
        $this->_contingency_fund_rate            = $config->contingency_fund_rate;
        $this->_operating_expense_rate           = $config->operating_expense_rate;
        $this->_rebates_account                  = $config->rebates_account;
        $this->_referral_bonus_account           = $config->referral_bonus_account;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * 
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'undeposited_funds_account'        => 92,
            'system_fee_account'               => 138,
            'contingency_fund_account'         => 139,
            'operating_expense_budget_account' => 140,
            'rebates_account'                  => 141,
            'referral_bonus_account'           => 142,
            'system_fee_rate'                  => 10.00,
            'contingency_fund_rate'            => 50.00,
            'operating_expense_rate'           => 60.00,
        ));

        parent::_initialize($config);
    }

    /**
     * Specialized save action, changing state by syncing
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionSync(KControllerContextInterface $context)
    {
        if(!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        if(count($entities))
        {
            foreach($entities as $entity) {
                $entity->setProperties($context->request->data->toArray());
            }

            //Only throw an error if the action explicitly failed.
            if ($entities->sync() === false)
            {
                $error = $entities->getStatusMessage();
                throw new KControllerExceptionActionFailed($error ? $error : 'Sync Action Failed');
            }
            else $context->response->setStatus(KHttpResponse::NO_CONTENT);
        }
        else throw new KControllerExceptionResourceNotFound('Resource Not Found');

        return $entities;
    }

    /**
     * Add
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionAdd(KControllerContextInterface $context)
    {
        if (is_null($context->request->data->DepositToAccountRef)) {
            $context->request->data->DepositToAccountRef = $this->_undeposited_funds_account;
        }

        return parent::_actionAdd($context);
    }
}