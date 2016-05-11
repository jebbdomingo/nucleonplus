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
            'accounting_service' => 'com:nucleonplus.accounting.service.transfer'
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
                if ($payout->status <> 'pending') {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Claim Status: Only pending claims can be processed"));
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
                if ($payout->status <> 'processing') {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Claim Status: Claim # {$payout->id} is not in processing"));
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
                // Commission amount
                $commission = $this->getObject('com:nucleonplus.model.rebates')
                    ->payout_id($payout->id)
                    ->fetch()
                ;
                $commAmount = 0;
                foreach ($commission as $comm) {
                    $commAmount += $comm->points;
                }

                // Direct referral amount
                $directReferral = $this->getObject('com:nucleonplus.model.referralbonuses')
                    ->payout_id($payout->id)
                    ->referral_type('dr')
                    ->fetch()
                ;
                $drAmount = 0;
                foreach ($directReferral as $dr) {
                    $drAmount += $dr->points;
                }

                // Indirect referral amount
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
                $total = ($commAmount + $drAmount + $irAmount);

                if ($total != $payout->amount) {
                    throw new Exception("There is a discrepancy in the Claim Request. Claim #{$payout->id}");
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
                if ($payout->status <> 'checkgenerated') {
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Cheque for Claim # {$payout->id} is not yet generated"));
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
        $config = JFactory::getConfig();
        $context->getRequest()->setData(array('status' => 'processing'));

        try
        {
            $payouts = parent::_actionEdit($context);

            foreach ($payouts as $payout)
            {
                // Transfer commission/dr/ir allocations to checking account for payout of claim
                $commission = $this->getObject('com:nucleonplus.model.rebates')
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
        catch (Exception $e)
        {
            $context->response->addMessage($e->getMessage(), 'exception');
        }

        return $payouts;
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
        $context->getRequest()->setData(array('status' => 'checkgenerated'));

        $payouts = parent::_actionEdit($context);

        foreach ($payouts as $payout)
        {
            // Send email notification
            $emailSubject = "A check has been generated for your Claim # {$payout->id}";
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
        $context->getRequest()->setData(['status' => 'disbursed']);

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
                $reward->status = 'claimed';
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
        
        $claimRequest->value = ($claimRequest->value != 'yes') ? 'yes' : 'no';
        $claimRequest->save();

        if (!$context->result instanceof KModelEntityInterface) {
            $claims = $this->getModel()->fetch();
        } else {
            $claims = $context->result;
        }

        return $claims;
    }
}