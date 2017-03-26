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
     * Override cancel to overwrite hardcoded button label
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandCancel(KControllerToolbarCommand $command)
    {
        $command->label = 'Back';
        $command->icon = 'icon-32-cancel';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'cancel',
                'data-novalidate' => 'novalidate',
            )
        ));
    }

    /**
     * Cancel Order Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandCancelorder(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs' => array(
                'data-action' => 'cancelorder'
            )
        ));

        $command->label = 'Cancel Order';
    }

    /**
     * Confirm Payment Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandConfirmpayment(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs' => array(
                'data-action' => 'confirm'
            )
        ));

        $command->label = 'Confirm Payment';
    }

    /**
     * Mark Delivered Command
     *
     * @param KControllerToolbarCommand $command
     *
     * @return void
     */
    protected function _commandMarkdelivered(KControllerToolbarCommand $command)
    {
        $command->append(array(
            'attribs' => array(
                'data-action' => 'markdelivered'
            )
        ));

        $command->label = 'Order Received';
    }

    /**
     *
     * @param KControllerContextInterface $context
     *
     * @return void
     */
    protected function _afterRead(KControllerContextInterface $context)
    {
        $controller = $this->getController();
        $allowed    = true;

        if (isset($context->result) && $context->result->isLockable() && $context->result->isLocked()) {
            $allowed = false;
        }

        if (is_null($context->result->id)) {
            $user    = $this->getObject('user');
            $account = $this->getObject('com:nucleonplus.model.accounts')->user_id($user->getId())->fetch();

            $this->addCommand('apply', array(
                'allowed' => ($allowed && !in_array($account->status, array('new', 'pending', 'terminated'))),
                'label'   => 'Place Order'
            ));

            if (in_array($account->status, array('new', 'pending'))) {
                $context->response->addMessage('Sorry you cannot place an order for now, your account is currently inactive', 'warning');
            }

            if ($account->status == 'terminated') {
                $context->response->addMessage('Your account was terminated for some reason, please contact Nucleon +', 'error');
            }
        }
        elseif ($context->result->order_status == 'awaiting_payment')
        {
            $this->addCommand('confirmpayment', array(
                'allowed' => $allowed,
            ));
            $this->addCommand('cancelorder', array(
                'allowed' => $allowed,
            ));
        }
        elseif ($context->result->order_status == 'shipped')
        {
            $this->addCommand('markdelivered', array(
                'allowed' => $allowed,
            ));
        }

        $this->removeCommand('save');
        $this->addCommand('cancel');
    }
}