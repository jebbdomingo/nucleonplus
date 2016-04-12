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
        $command->href  = 'view=member';
        $command->label = 'New Member';
    }

    protected function _commandActivate(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'activate',
                'data-novalidate' => 'novalidate' // This is needed for koowa-grid and view without form
            )
        ));

        $command->label = 'Activate';
    }

    protected function _commandPlaceorder(KControllerToolbarCommand $command)
    {
        $command->icon  = 'icon-32-new';
        $command->label = 'Place an Order';
    }

    protected function _commandPos(KControllerToolbarCommand $command)
    {
        $command->icon  = 'icon-32-new';
        $command->label = 'POS';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        parent::_afterRead($context);
        
        $this->_addReadCommands($context);
    }

    protected function _afterBrowse(KControllerContextInterface $context)
    {
        parent::_afterBrowse($context);
        
        $this->_addBrowseCommands($context);
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

        if ($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('edit', [
                'href' => 'view=member&id=' . $context->result->user_id
            ]);
        }

        if ($controller->isEditable() && $controller->canSave() && !in_array($context->result->status, array('new', 'pending'))) {
            $this->addCommand('placeorder', [
                'allowed' => $allowed,
                'href'    => 'view=order&account_id=' . $context->result->id
            ]);
        }

        if ($controller->isEditable() && $controller->canSave() && !in_array($context->result->status, array('new', 'pending'))) {
            $this->addCommand('pos', [
                'allowed' => $allowed,
                'href' => 'view=order&account_id=' . $context->result->id . '&layout=pos'
            ]);
        }

        if ($controller->isEditable() && $controller->canSave() && $context->result->status == 'pending') {
            $this->addCommand('activate', [
                'allowed' => $allowed,
            ]);
        }

        $this->addCommand('back', array(
            'href'  => 'option=com_' . $controller->getIdentifier()->getPackage() . '&view=accounts',
            'label' => 'Back to List'
        ));

        if (in_array($context->result->status, array('new', 'pending'))) {
            $context->response->addMessage('This account is currently inactive', 'warning');
        }
    }

    /**
     * Add browse view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _addBrowseCommands(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if ($controller->isEditable() && $controller->canSave()) {
            $this->addCommand('activate', [
                'allowed' => $allowed,
            ]);
        }
    }
}