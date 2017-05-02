<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerToolbarCart extends ComKoowaControllerToolbarActionbar
{
    protected function _commandBack(KControllerToolbarCommand $command)
    {
        $command->label = 'Back';
        $command->icon  = 'k-icon-action-undo k-icon--error';
        $command->href  = 'view=cart&layout=';
    }

    protected function _commandUpdate(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-pencil';

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'updatecart',
                'data-novalidate' => 'novalidate', // This is needed for koowa-grid and view without form
                'accesskey'       => 'u'
            )
        ));

        $command->label = 'Update Cart';
    }

    protected function _commandCheckout(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-cart k-icon--success';

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'add',
                'data-novalidate' => 'novalidate', // This is needed for koowa-grid and view without form
                'accesskey'       => 'c'
            )
        ));

        $command->label = 'Checkout';
    }

    protected function _commandConfirm(KControllerToolbarCommand $command)
    {
        $command->icon = 'k-icon-enabled k-icon--success';

        $command->append(array(
            'attribs' => array(
                'data-action'     => 'confirm',
                'data-novalidate' => 'novalidate', // This is needed for koowa-grid and view without form
                'accesskey'       => 'c'
            )
        ));

        $command->label = 'Confirm';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
        $this->_addReadCommands($context);

        // parent::_afterRead($context);
        
        // $this->removeCommand('apply');
        // $this->removeCommand('save');
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

        if ($controller->isEditable())
        {
            if ($context->request->query->layout == 'confirm') {
                $this->addCommand('checkout', [
                    'allowed' => $allowed,
                ]);
                $this->addCommand('back');
                $this->removeCommand('cancel');
            } else {
                $this->addCommand('update', [
                    'allowed' => $allowed,
                ]);
                $this->addCommand('confirm', [
                    'allowed' => $allowed,
                ]);
                $this->addCommand('cancel');
            }
        }
    }
}
