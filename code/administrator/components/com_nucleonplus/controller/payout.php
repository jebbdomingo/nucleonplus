<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <http://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */


class ComNucleonplusControllerPayout extends ComKoowaControllerModel
{
    /**
     * Generate check
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionGeneratecheck(KControllerContextInterface $context)
    {
        $context->getRequest()->setData(['status' => 'checkgenerated']);

        return parent::_actionEdit($context);
    }

    /**
     * Disburse
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionDisburse(KControllerContextInterface $context)
    {
        $context->getRequest()->setData(['status' => 'disbursed']);

        $payouts = parent::_actionEdit($context);

        foreach ($payouts as $payout)
        {
            $reward = $this->getObject('com:nucleonplus.model.rewards')
                ->payout_id($payout->id)
                ->status('ready')
                ->fetch()
            ;

            if ($reward->id)
            {
                $reward->status = 'claimed';
                $reward->save();
            }
        }

        return $payouts;
    }
}