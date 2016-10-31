<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerToolbarCart extends ComCartControllerToolbarCart
{
    /**
     * Add read view toolbar buttons
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _addReadCommands(KControllerContextInterface $context)
    {
        parent::_addReadCommands($context);
        
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        $account_id = ($controller->getRequest()->query->customer);
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->id($account_id)->fetch();

        if (in_array($account->status, array('new', 'pending'))) {
            $context->response->addMessage('This account is currently inactive', 'warning');
        }
    }
}
