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
        parent::_afterRead($context);
        
        $controller = $this->getController();
        $canSave    = ($controller->isEditable() && $controller->canSave());
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if ($canSave && ($context->result->status == 'pending'))
        {
            $this->addCommand('processing', [
                'allowed' => $allowed
            ]);
        }

        if ($canSave && ($context->result->status == 'processing'))
        {
            $this->addCommand('generatecheck', [
                'allowed' => $allowed
            ]);
        }

        if ($canSave && ($context->result->status == 'checkgenerated'))
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
            // Batch processing
            $this->addCommand('processing', [
                'allowed' => $allowed
            ]);

            // Batch generate check
            $this->addCommand('generatecheck', [
                'allowed' => $allowed
            ]);

            // Batch disburse
            $this->addCommand('disburse', [
                'allowed' => $allowed
            ]);

            // Toggle claim request command
            $claimRequest = $this->getObject('com:nucleonplus.model.configs')->item('claim_request')->fetch();

            $this->addCommand('toggleclaimrequest', [
                'allowed' => $allowed,
                'label'   => ($claimRequest->value == 'yes') ? 'Turn-off Claim Request' : 'Turn-on Claim Request',
                'icon'    => ($claimRequest->value == 'yes') ? 'icon-32-stop' : 'icon-32-save'
            ]);
        }
    }
}