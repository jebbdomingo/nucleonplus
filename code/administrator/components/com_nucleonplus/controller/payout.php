<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <http://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */


class ComNucleonplusControllerPayout extends ComKoowaControllerModel
{
    /**
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_accounting_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        @ini_set('max_execution_time', 300);
        
        parent::__construct($config);

        $this->addCommandCallback('before.processing', '_validatePayout');
        $this->addCommandCallback('before.processing', '_validateProcessing');
        $this->addCommandCallback('before.generatecheck', '_validatePayout');
        $this->addCommandCallback('before.generatecheck', '_validateCheckgenerated');
        $this->addCommandCallback('before.disburse', '_validatePayout');
        $this->addCommandCallback('before.disburse', '_validateDisburse');

        // Sales Receipt Service
        $identifier = $this->getIdentifier($config->accounting_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceTransferInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceTransferInterface"
            );
        }
        else $this->_accounting_service = $service;
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
            'accounting_service' => 'com:nucleonplus.accounting.service.transfer',
            'behaviors'          => array(
                'masspayable',
                'connectable',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Validate processing action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateProcessing(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $payouts = $this->getModel()->fetch();
        } else {
            $payouts = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($payouts as $payout)
            {
                if ($payout->status <> ComNucleonplusModelEntityPayout::PAYOUT_STATUS_PENDING) {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Only pending payouts can be processed"));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Validate check generated action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateCheckgenerated(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $payouts = $this->getModel()->fetch();
        } else {
            $payouts = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($payouts as $payout)
            {
                if ($payout->status <> ComNucleonplusModelEntityPayout::PAYOUT_STATUS_PROCESSING) {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Payout # {$payout->id} is not in processing"));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Validate payout
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validatePayout(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $payouts = $this->getModel()->fetch();
        } else {
            $payouts = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($payouts as $payout)
            {
                // Rebates amount
                $rebates = $this->getObject('com:nucleonplus.model.rebates')
                    ->payout_id($payout->id)
                    ->fetch()
                ;
                $rebatesAmount = 0;
                foreach ($rebates as $rebate) {
                    $rebatesAmount += $rebate->points;
                }

                // Direct referral bonus amount
                $drBonuses = $this->getObject('com:nucleonplus.model.directreferrals')
                    ->payout_id($payout->id)
                    ->fetch()
                ;
                $drBonusAmount = 0;
                foreach ($drBonuses as $drBonus) {
                    $drBonusAmount += $drBonus->points;
                }

                // Commission amount
                $commission = $this->getObject('com:nucleonplus.model.patronagebonuses')
                    ->payout_id($payout->id)
                    ->fetch()
                ;
                $commAmount = 0;
                foreach ($commission as $comm) {
                    $commAmount += $comm->points;
                }

                // Unilevel direct referral amount
                $directReferral = $this->getObject('com:nucleonplus.model.referralbonuses')
                    ->payout_id($payout->id)
                    ->referral_type('dr')
                    ->fetch()
                ;
                $drAmount = 0;
                foreach ($directReferral as $dr) {
                    $drAmount += $dr->points;
                }

                // Unilevel indirect referral amount
                $indirectReferral = $this->getObject('com:nucleonplus.model.referralbonuses')
                    ->payout_id($payout->id)
                    ->referral_type('ir')
                    ->fetch()
                ;
                $irAmount = 0;
                foreach ($indirectReferral as $ir) {
                    $irAmount += $ir->points;
                }

                // Validate commissions/referral payout computation
                $total = ($rebatesAmount + $drBonusAmount + $commAmount + $drAmount + $irAmount);

                if ($total != $payout->amount) {
                    throw new Exception("There is a discrepancy in the Payout Request. Payout #{$payout->id}");
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Validate disbursed action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateDisburse(KControllerContextInterface $context)
    {
        if (!$context->result instanceof KModelEntityInterface) {
            $payouts = $this->getModel()->fetch();
        } else {
            $payouts = $context->result;
        }

        try
        {
            $translator = $this->getObject('translator');

            foreach ($payouts as $payout)
            {
                if ($payout->status <> ComNucleonplusModelEntityPayout::PAYOUT_STATUS_CHECK_GENERATED) {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Check for Payout # {$payout->id} is not yet generated"));
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();
        }
    }

    /**
     * Processing
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionProcessing(KControllerContextInterface $context)
    {
        try
        {
            if (!$context->result instanceof KModelEntityInterface) {
                $payouts = $this->getModel()->fetch();
            } else {
                $payouts = $context->result;
            }

            if (count($payouts))
            {
                $config = $this->getObject('com://admin/nucleonplus.model.configs')
                    ->item(ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_NAME)
                    ->fetch()
                ;

                $data = array(
                    'status'         => ComNucleonplusModelEntityPayout::PAYOUT_STATUS_PROCESSING,
                    'date_processed' => date('Y-m-d H:i:s'),
                );

                foreach($payouts as $payout)
                {
                    if ($payout->payout_method == ComNucleonplusModelEntityPayout::PAYOUT_METHOD_FUNDS_TRANSFER) {
                        $data['run_date'] = date('Y-m-d', strtotime($config->value));
                    }

                    $payout->setProperties($data);
                }

                // Only set the reset content status if the action explicitly succeeded
                if ($payouts->save() === true) {
                    $context->response->setStatus(KHttpResponse::RESET_CONTENT);
                }

                $this->_fundCheck($payouts);
            }
            else throw new KControllerExceptionResourceNotFound('Resource could not be found');
        }
        catch (Exception $e)
        {
            $context->response->addMessage($e->getMessage(), 'exception');
        }

        return $payouts;
    }

    protected function _fundCheck($payouts)
    {
        foreach ($payouts as $payout)
        {
            // Transfer bonus allocations to checking account for payout
            $rebates = $this->getObject('com:nucleonplus.model.rebates')
                ->payout_id($payout->id)
                ->fetch()
            ;

            $drBonuses = $this->getObject('com:nucleonplus.model.directreferrals')
                ->payout_id($payout->id)
                ->fetch()
            ;

            $commission = $this->getObject('com:nucleonplus.model.patronagebonuses')
                ->payout_id($payout->id)
                ->fetch()
            ;

            $directReferral = $this->getObject('com:nucleonplus.model.referralbonuses')
                ->payout_id($payout->id)
                ->referral_type('dr')
                ->fetch()
            ;

            $indirectReferral = $this->getObject('com:nucleonplus.model.referralbonuses')
                ->payout_id($payout->id)
                ->referral_type('ir')
                ->fetch()
            ;

            if (count($rebates) > 0)
            {
                $amount = 0;
                foreach ($rebates as $rebate) {
                    $amount += $rebate->points;
                }
                $this->_accounting_service->rebatesCheck($payout->id, $amount);
            }

            if (count($drBonuses) > 0)
            {
                $amount = 0;
                foreach ($drBonuses as $drBonus) {
                    $amount += $drBonus->points;
                }
                $this->_accounting_service->directReferralBonusCheck($payout->id, $amount);
            }

            if (count($commission) > 0)
            {
                $amount = 0;
                foreach ($commission as $comm) {
                    $amount += $comm->points;
                }
                $this->_accounting_service->commissionCheck($payout->id, $amount);
            }

            if (count($directReferral) > 0)
            {
                $amount = 0;
                foreach ($directReferral as $dr) {
                    $amount += $dr->points;
                }
                $this->_accounting_service->directReferralCheck($payout->id, $amount);
            }

            if (count($indirectReferral) > 0)
            {
                $amount = 0;
                foreach ($indirectReferral as $ir) {
                    $amount += $ir->points;
                }
                $this->_accounting_service->indirectReferralCheck($payout->id, $amount);
            }
        }
    }

    /**
     * Generate check
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionGeneratecheck(KControllerContextInterface $context)
    {
        $config = JFactory::getConfig();
        $context->getRequest()->setData(array(
            'status'               => ComNucleonplusModelEntityPayout::PAYOUT_STATUS_CHECK_GENERATED,
            'date_check_generated' => gmdate('Y-m-d H:i:s')
        ));

        $payouts = parent::_actionEdit($context);

        foreach ($payouts as $payout)
        {
            // Send email notification
            $emailSubject = "A check has been generated for your Claim #{$payout->id}";
            $emailBody    = JText::sprintf(
                'COM_NUCLEONPLUS_PAYOUT_EMAIL_CHECK_GENERATED_BODY',
                $payout->name,
                $payout->id,
                JUri::root()
            );

            $mail = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $payout->email, $emailSubject, $emailBody);
            // Check for an error.
            if ($mail !== true) {
                $context->response->addMessage(JText::_('COM_NUCLEONPLUS_PAYOUT_EMAIL_SEND_MAIL_FAILED'), 'error');
            }
        }

        return $payouts;
    }

    /**
     * Disburse
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionDisburse(KControllerContextInterface $context)
    {
        $context->getRequest()->setData(array(
            'status'         => ComNucleonplusModelEntityPayout::PAYOUT_STATUS_DISBURSED,
            'date_disbursed' => gmdate('Y-m-d H:i:s')
        ));

        $payouts = parent::_actionEdit($context);

        foreach ($payouts as $payout)
        {
            $reward = $this->getObject('com:nucleonplus.model.rewards')
                ->payout_id($payout->id)
                ->status('processing')
                ->fetch()
            ;

            if ($reward->id)
            {
                $reward->status = ComNucleonplusModelEntityReward::ACTIVE_CLAIMED;
                $reward->save();
            }
        }

        return $payouts;
    }

    /**
     * Toggle claim request
     * We disable claim request on cut-off time (i.e. every Thursday 1PM) while we are processing checks
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionToggleclaimrequest(KControllerContextInterface $context)
    {
        $claimRequest = $this->getObject('com:nucleonplus.model.configs')->item('claim_request')->fetch();
        
        $claimRequest->value = ($claimRequest->value != ComNucleonplusModelEntityConfig::CLAIM_REQUEST_ENABLED) ? ComNucleonplusModelEntityConfig::CLAIM_REQUEST_ENABLED : ComNucleonplusModelEntityConfig::CLAIM_REQUEST_DISABLED;
        $claimRequest->save();

        if (!$context->result instanceof KModelEntityInterface) {
            $claims = $this->getModel()->fetch();
        } else {
            $claims = $context->result;
        }

        return $claims;
    }
}