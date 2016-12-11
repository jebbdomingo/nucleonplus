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
     * Ship order Command
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
     * Ship order Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandCancelorder(KControllerToolbarCommand $command)
    {
        $translator    = $this->getObject('translator');
        $command->icon = 'icon-32-delete';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'cancelorder',
                'data-prompt' => $translator->translate('Cancelled order cannot be recovered. Would you like to continue?'),
            )
        ));

        $command->label = 'Cancel Order';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        parent::_afterRead($context);

        $this->removeCommand('save');
        $this->removeCommand('apply');
        $this->removeCommand('cancel');

        $controller = $this->getController();
        $canSave    = ($controller->isEditable() && $controller->canSave());
        $allowed    = true;

        $this->addCommand('back', array(
            'href'  => 'option=com_' . $controller->getIdentifier()->getPackage() . '&view=orders',
            'label' => 'Back to List'
        ));

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        // Process order command
        if ($controller->canProcess())
        {
            $this->addCommand('processing', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Ship order command
        if ($canSave && ($context->result->order_status == ComNucleonplusModelEntityOrder::STATUS_PROCESSING))
        {
            $this->addCommand('ship', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Mark order as delivered command
        if ($canSave && ($context->result->order_status == ComNucleonplusModelEntityOrder::STATUS_SHIPPED))
        {
            $this->addCommand('markdelivered', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Mark order as completed command
        if ($canSave && ($context->result->order_status == ComNucleonplusModelEntityOrder::STATUS_DELIVERED))
        {
            $this->addCommand('markcompleted', [
                'allowed' => $allowed,
                'attribs' => ['data-novalidate' => 'novalidate']
            ]);
        }

        // Cancel command
        if ($canSave && (in_array($context->result->order_status, array(ComNucleonplusModelEntityOrder::STATUS_PENDING,ComNucleonplusModelEntityOrder::STATUS_PAYMENT))))
        {
            $this->addCommand('cancelorder', [
                'allowed' => $allowed,
                'attribs' => array('data-novalidate' => 'novalidate')
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

        $this->removeCommand('new');
        $this->removeCommand('delete');

        // Mark order as in processing command
        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('processing', [
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

        // Cancel command
        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('cancelorder', [
                'allowed' => $allowed
            ]);
        }
    }
}