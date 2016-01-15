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
     * Paid Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @todo Find appropriate close icon
     *
     * @return void
     */
    protected function _commandMarkpaid(KControllerToolbarCommand $command)
    {
        $command->icon = 'icon-32-save';

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'markpaid',
                'data-novalidate' => 'novalidate' // This is needed for koowa-grid
            )
        ));

        $command->label = 'Confirm Payment & Allocate Slot(s)';
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
    protected function _commandCancel(KControllerToolbarCommand $command)
    {
        $command->label = 'Back';

        parent::_commandCancel($command);
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        $this->_addInvoiceCommands($context);
        $this->_addReadCommands($context);
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

        $this->addCommand('cancel'  );
    }

    /**
     * Add purchase view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _addInvoiceCommands(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if ($controller->isEditable() && $controller->canSave() && $context->result->invoice_status <> 'paid')
        {
            $this->addCommand('markpaid', ['allowed' => $allowed]);
        }
    }
}
