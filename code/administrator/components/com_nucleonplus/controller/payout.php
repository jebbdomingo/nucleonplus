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
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.generatecheck', '_validateCheckGenerated');
        $this->addCommandCallback('before.disburse', '_validateDisburse');
    }

    /**
     * Validate check generated action
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateCheckGenerated(KControllerContextInterface $context)
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
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Payout # {$payout->id} is pending"));
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
                    throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Payout Status: Payout # {$payout->id} is not yet claimed"));
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
     * Generate check
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionGeneratecheck(KControllerContextInterface $context)
    {
        $context->getRequest()->setData(array('status' => 'checkgenerated'));

        $payout = parent::_actionEdit($context);

        // Send email notification
        $config = JFactory::getConfig();

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

        return $payout;
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
}