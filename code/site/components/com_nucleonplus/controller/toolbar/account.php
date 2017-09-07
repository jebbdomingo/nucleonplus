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
    protected function _commandEncash(KControllerToolbarCommand $command)
    {
        $command->icon  = 'k-icon-dollar';
        $command->href  = 'view=payout&layout=form';
        $command->label = 'Encash';
    }

    protected function _afterRead(KControllerContextInterface $context)
    {
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

        if ($controller->isEditable())
        {
            if ($context->request->query->layout == 'rewards')
            {
                $controller = $this->getObject('com://site/nucleonplus.controller.payout');

                if ($controller->checkMinimumAmount())
                {
                    $this->addCommand('encash', [
                        'allowed' => $allowed,
                    ]);
                }
                else
                {
                    $config = $this->getObject('com://site/rewardlabs.model.configs')
                        ->item(ComNucleonplusModelEntityConfig::PAYOUT_MIN_AMOUNT_NAME)
                        ->fetch()
                    ;
                    $amount  = number_format((float) $config->value, 2);
                    
                    $context->response->addMessage("Minimum amount for each payout request is &#8369;{$amount}", KControllerResponse::FLASH_WARNING);
                }
            }
        }
    }
}
