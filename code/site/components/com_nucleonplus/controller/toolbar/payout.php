<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerToolbarPayout extends ComKoowaControllerToolbarActionbar
{
    /**
     * Override parent _afterRead
     *
     * @param KControllerContextInterface $context A command context object
     */
    protected function afterRead(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $translator = $this->getObject('translator');
        $name       = $translator->translate(strtolower($context->subject->getIdentifier()->name));

        if ($controller->getModel()->getState()->isUnique()) {
            $title = $translator->translate('Edit {item_type}', array('item_type' => $name));
        } else {
            $title = $translator->translate('Submit New {item_type} Request', array('item_type' => ucfirst($name)));
        }

        $this->getCommand('title')->title = $title;

        KControllerToolbarActionbar::_afterRead($context);
    }

    /**
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _afterRead(KControllerContextInterface $context)
    {
        $this->afterRead($context);

        $this->removeCommand('save');

        $controller   = $this->getController();
        $claimRequest = $this->getObject('com://site/rewardlabs.model.configs')->item('claim_request')->fetch();
        $allowed      = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        // Claim request notification
        if ($claimRequest->value == ComRewardlabsModelEntityConfig::CLAIM_REQUEST_DISABLED) {
            $context->response->addMessage('Claim request is not available at the moment, please check the cut-off time for claim requests', KControllerResponse::FLASH_WARNING);
        }

        // Outstanding payout request notification
        if ($context->result->isNew() && $controller->hasOutstandingRequest()) {
            $context->response->addMessage('You have outstanding payout request', KControllerResponse::FLASH_WARNING);
        }

        if ($context->result->isNew() && !$controller->checkMinimumAmount())
        {
            $config = $this->getObject('com://site/rewardlabs.model.configs')
                ->item(ComRewardlabsModelEntityConfig::PAYOUT_MIN_AMOUNT_NAME)
                ->fetch()
            ;
            $amount  = number_format((float) $config->value, 2);
            $message = "Minimum amount for each payout request is &#8369;{$amount}";
            $context->response->addMessage($message, KControllerResponse::FLASH_WARNING);
        }

        // Bank account details notification
        if (!$controller->checkBankDetails())
        {
            $url  = JRoute::_('index.php?option=com_nucleonplus&view=member&tmpl=koowa&layout=form', false);
            $link = '<a href="' . $url . '">here</a>';
            $context->response->addMessage("Please complete your bank account details and mobile # in your profile. Click {$link} to update your profile.", KControllerResponse::FLASH_WARNING);
        }
    }
}