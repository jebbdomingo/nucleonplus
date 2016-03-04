<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerToolbarOrder extends ComKoowaControllerToolbarActionbar
{
    /**
     * Confirm payment Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandMarkpaid(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'verifypayment'
            )
        ));

        $command->label = 'Verify Payment';
    }

    /**
     * Ship order Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandShip(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'ship'
            )
        ));

        $command->label = 'Ship';
    }

    /**
     * Mark order as delivered Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandMarkdelivered(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'markdelivered'
            )
        ));

        $command->label = 'Mark as Delivered';
    }

    /**
     * Mark order as completed Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandMarkcompleted(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'markcompleted'
            )
        ));

        $command->label = 'Mark as Completed';
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

        // Verify payment command
        if ($canSave && ($context->result->order_status == 'verifying'))
        {
            $this->addCommand('markpaid', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Ship order command
        if ($canSave && ($context->result->order_status == 'processing'))
        {
            $this->addCommand('ship', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Mark order as delivered command
        if ($canSave && ($context->result->order_status == 'shipped'))
        {
            $this->addCommand('markdelivered', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Mark order as completed command
        if ($canSave && ($context->result->order_status == 'delivered'))
        {
            $this->addCommand('markcompleted', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }
    }

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        parent::_afterBrowse($context);

        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        $this->removeCommand('delete');

        /*if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('processrebates', ['allowed' => $allowed]);
        }

        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('markpaid', [
                'allowed' => $allowed
            ]);
        }*/
    }

    /**
     * Back button
     *
     * Just rename the toolbar button
     *
     * @param KControllerToolbarCommand $command
     *
     * @return
     */
    /*protected function _commandCancel(KControllerToolbarCommand $command)
    {
        $command->label = 'Back To List';

        parent::_commandCancel($command);
    }*/

    /*protected function _commandProcessrebates(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'processrebates'
                //'data-novalidate' => 'novalidate' // This is needed for koowa-grid
            )
        ));

        $command->label = 'Process Rebates';
    }*/

    /**
     * Add read view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    /*protected function _addReadCommands(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        $this->addCommand('cancel');
    }*/
}