<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerToolbarAccount extends ComKoowaControllerToolbarActionbar
{
    protected function _commandNew(KControllerToolbarCommand $command)
    {
        $command->href  = 'view=account&layout=form';
        $command->label = 'Create New Account';
    }

    /**
     * Close Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @todo Find appropriate close icon
     *
     * @return void
     */
    protected function _commandClose(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-stop';

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'close',
                'data-novalidate' => 'novalidate' // This is needed for koowa-grid
            )
        ));
    }

    protected function _commandPlaceorder(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'save'
            )
        ));

        $command->label = 'Place Order';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        $this->_addPurchaseCommands($context);
        $this->_addEditCommands($context);
        $this->_addReadCommands($context);
    }

    /**
     * Add edit view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _addEditCommands(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if ($controller->isEditable() && $controller->canApply())
        {
            $this->addCommand('apply', array(
                'allowed' => $allowed,
                'label'   => 'Apply Changes'
            ));
        }

        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('save', array(
                'allowed' => $allowed,
                'label'   => 'Save'
            ));
        }

        if ($controller->isEditable() && $controller->canCancel()) {
            $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));
        }
    }

    /**
     * Add read view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _addReadCommands(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        // Close ticket action button
        $status = $controller->getModel()->fetch()->status;

        if ((!is_null($status) && $status <> 'closed') && $controller->isEditable()) {
            $this->addCommand('close', ['allowed' => $allowed]);
        }

        if ($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('edit', array('href' => 'layout=form'));
        }

        $this->addCommand('back', array(
            'href'  => 'option=com_' . $controller->getIdentifier()->getPackage() . '&view=accounts',
            'label' => 'Back to List'
        ));
    }

    /**
     * Add purchase view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _addPurchaseCommands(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if ($controller->isEditable() && $controller->canSave())
        {
            $this->addCommand('placeorder', ['allowed' => $allowed]);
        }
    }
}
