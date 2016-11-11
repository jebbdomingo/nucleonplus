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
     * Toggle claim request Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandToggleclaimrequest(KControllerToolbarCommand $command)
    {
        //$command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'toggleclaimrequest',
                'data-novalidate' => 'novalidate'
            )
        ));
    }

    /**
     * Processing Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandProcessing(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'processing'
            )
        ));

        $command->label = 'Processing';
    }

    /**
     * Generate check Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandGeneratecheck(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'generatecheck'
            )
        ));

        $command->label = 'Check Generated';
    }

    /**
     * Disburse Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandDisburse(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'disburse'
            )
        ));

        $command->label = 'Disbursed';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $canSave    = ($controller->isEditable() && $controller->canSave());
        $allowed    = true;

        $this->addCommand('back', array(
            'href'  => 'option=com_' . $controller->getIdentifier()->getPackage() . '&view=payouts',
            'label' => 'Back to List'
        ));

        parent::_afterRead($context);

        $this->removeCommand('apply');
        $this->removeCommand('save');
        $this->removeCommand('cancel');
        
        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if ($canSave && ($context->result->status == ComNucleonplusModelEntityPayout::PAYOUT_STATUS_PENDING))
        {
            if ($controller->canProcessing())
            {
                $this->addCommand('processing', [
                    'allowed' => $allowed
                ]);

                $config = $this->getObject('com://admin/nucleonplus.model.configs')
                    ->item(ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_NAME)
                    ->fetch()
                ;

                $date = date('M d, Y', strtotime($config->value));
                $url  = JRoute::_('index.php?option=com_nucleonplus&view=config&id=' . ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_ID, false);
                $link = '<a href="' . $url . '">' . $date . '</a>';
                $context->response->addMessage("Payout Processing Run Date: <strong>{$link}</strong>", 'info');
            }
            else
            {
                $url  = JRoute::_('index.php?option=com_nucleonplus&view=config&id=' . ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_ID, false);
                $link = '<a href="' . $url . '">here</a>';

                $context->response->addMessage("Set payout run date {$link} before processing", 'warning');
            }
        }

        if ($canSave && ($context->result->status == ComNucleonplusModelEntityPayout::PAYOUT_STATUS_PROCESSING && $context->result->payout_method == ComNucleonplusModelEntityPayout::PAYOUT_METHOD_PICKUP))
        {
            $this->addCommand('generatecheck', [
                'allowed' => $allowed
            ]);
        }

        if ($canSave && ($context->result->status == ComNucleonplusModelEntityPayout::PAYOUT_STATUS_CHECK_GENERATED && $context->result->payout_method == ComNucleonplusModelEntityPayout::PAYOUT_METHOD_PICKUP))
        {
            $this->addCommand('disburse', [
                'allowed' => $allowed
            ]);
        }
    }

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        parent::_afterBrowse($context);

        $controller = $this->getController();
        $canSave    = ($controller->isEditable() && $controller->canSave());
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        // We do not allow manual addition and deletion of entity
        $this->removeCommand('new');
        $this->removeCommand('delete');

        if ($canSave)
        {
            // Batch payout processing
            if ($allowed && $controller->canProcessing())
            {
                $this->addCommand('processing', [
                    'allowed' => $allowed && $controller->canProcessing()
                ]);

                $config = $this->getObject('com://admin/nucleonplus.model.configs')
                    ->item(ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_NAME)
                    ->fetch()
                ;

                $date = date('M d, Y', strtotime($config->value));
                $url  = JRoute::_('index.php?option=com_nucleonplus&view=config&id=' . ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_ID, false);
                $link = '<a href="' . $url . '">' . $date . '</a>';
                $context->response->addMessage("Payout Processing Run Date: <strong>{$link}</strong>", 'info');
            }
            else
            {
                $url  = JRoute::_('index.php?option=com_nucleonplus&view=config&id=' . ComNucleonplusModelEntityConfig::PAYOUT_RUN_DATE_ID, false);
                $link = '<a href="' . $url . '">here</a>';

                $context->response->addMessage("Set payout run date {$link} before processing", 'warning');
            }

            // Batch generate check
            if ($canSave && $context->request->query->payout_method == ComNucleonplusModelEntityPayout::PAYOUT_METHOD_PICKUP)
            {
                $this->addCommand('generatecheck', [
                    'allowed' => $allowed
                ]);
            }

            // Batch disburse
            $this->addCommand('disburse', [
                'allowed' => $allowed
            ]);

            // Toggle claim request command
            $claimRequest = $this->getObject('com:nucleonplus.model.configs')->item('claim_request')->fetch();

            $this->addCommand('toggleclaimrequest', [
                'allowed' => $allowed,
                'label'   => ($claimRequest->value == ComNucleonplusModelEntityConfig::CLAIM_REQUEST_ENABLED) ? 'Disable Claim Request' : 'Enable Claim Request',
                'icon'    => ($claimRequest->value == ComNucleonplusModelEntityConfig::CLAIM_REQUEST_ENABLED) ? 'icon-32-stop' : 'icon-32-save'
            ]);
        }
    }
}