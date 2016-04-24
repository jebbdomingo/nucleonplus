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
     * Activate Reward Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandActivatereward(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'activatereward'
            )
        ));

        $command->label = 'Activate Reward';
    }

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

        $command->label = 'Verify Payment &amp; Activate Reward';
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

    /**
     * Void Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandVoid(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'void'
            )
        ));

        $command->label = 'Void';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        parent::_afterRead($context);

        $this->removeCommand('save');

        // Disallow direct editing once has been created
        if ($context->result->order_status)
        {
            $this->removeCommand('apply');
        }
        
        $controller = $this->getController();
        $canSave    = ($controller->isEditable() && $controller->canSave());
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        // Verify payment command
        if ($canSave && ($context->result->order_status == 'awaiting_verification'))
        {
            $this->addCommand('markpaid', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Acivate reward
        // In rare case that a reward isn't activated when the order is paid we can use this button to activate the reward
        if ($canSave && $context->result->_reward_status == 'pending' && (in_array($context->result->order_status, array('processing', 'completed'))))
        {
            $this->addCommand('activatereward', [
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

        // Void command
        if ($canSave && (in_array($context->result->order_status, array('awaiting_payment', 'awaiting_verification'))))
        {
            $this->addCommand('void', [
                'allowed' => $allowed
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

        // Verify payment command
        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('markpaid', [
                'allowed' => $allowed
            ]);
        }

        // Mark order as delivered command
        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('markdelivered', [
                'allowed' => $allowed
            ]);
        }

        // Mark order as completed command
        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('markcompleted', [
                'allowed' => $allowed
            ]);
        }

        // Void command
        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('void', [
                'allowed' => $allowed
            ]);
        }
    }
}