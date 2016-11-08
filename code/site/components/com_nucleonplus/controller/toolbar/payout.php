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
    protected function _commandCancel(KControllerToolbarCommand $command)
    {
        $command->label = 'Back';
        $command->icon  = 'icon-32-cancel';
        $command->href  = 'view=account&tmpl=index';
    }

    /**
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _afterRead(KControllerContextInterface $context)
    {
        parent::_afterRead($context);

        $this->removeCommand('save');

        $controller   = $this->getController();
        $user         = $this->getObject('user');
        $account      = $this->getObject('com:nucleonplus.model.accounts')->id($user->getId())->fetch();
        $claimRequest = $this->getObject('com:nucleonplus.model.configs')->item('claim_request')->fetch();
        $allowed      = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        // Claim request notification
        if ($claimRequest->value == ComNucleonplusModelEntityConfig::CLAIM_REQUEST_DISABLED) {
            $context->response->addMessage('Claim request is not available at the moment, please check the cut-off time for claim requests', 'warning');
        }

        // Outstanding payout request notification
        if ($context->result->isNew() && $controller->getModel()->hasOutstandingRequest($account->id)) {
            $context->response->addMessage('You have outstanding payout request', 'warning');
        }

        // Bank account details notification
        $acctNo   = trim($account->bank_account_number);
        $acctName = trim($account->bank_account_name);
        $mobile   = trim($account->mobile);

        if (empty($acctNo) || empty($acctName) || empty($mobile))
        {
            $url  = JRoute::_('index.php?option=com_nucleonplus&view=member&tmpl=koowa&layout=form', false);
            $link = '<a href="' . $url . '">here</a>';
            $context->response->addMessage("Please complete your bank account details and mobile # in your profile. Click {$link} to update your profile.", 'warning');
        }
    }
}