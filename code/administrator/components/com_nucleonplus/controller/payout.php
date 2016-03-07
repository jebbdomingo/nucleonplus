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
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.add', '_validate');
    }

    /**
     * Validate and construct data
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validate(KControllerContextInterface $context)
    {
        $contextData  = $context->request->data;
        $user         = $this->getObject('user');
        $account      = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user->id)->fetch();
        $totalRebates = 0;

        // Ensure there is no discrepancy in member's requested payout against his rebates
        foreach ($contextData->rewards as $id)
        {
            $rebates = $this->getObject('com://admin/nucleonplus.model.rewards')
                ->status('ready')
                ->customer_id($account->id)
                ->getRebatesByReward($id)
            ;

            if (is_null($rebates->id)) {
                throw new Exception("There is discrepancy in your request ref# {$id}");
                return false;
            }

            foreach ($rebates as $rebate)
            {
                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($rebate->reward_id_from)
                    ->fetch()
                ;

                if ($rewardFrom->prpv <> $rebate->points) {
                    throw new Exception("There is a discrepancy in your rewards ref# {$rewardFrom->id}-{$rebate->id}");
                    return false;
                }
                else $totalRebates += $rewardFrom->prpv;
            }
        }

        $data = new KObjectConfig([
            'status'     => 'pending',
            'account_id' => $account->id,
            'amount'     => $contextData->direct_referral + $contextData->indirect_referral + $totalRebates
        ]);

        $context->getRequest()->setData($data->toArray());

        return true;
    }

    /**
     *
     * @param KControllerContextInterface $context
     *
     * @return entity
     */
    protected function _actionEdit(KControllerContextInterface $context)
    {
        $contextData = $context->request->data;

        $context->getRequest()->setData(['status' => $contextData->status]);

        return parent::_actionEdit($context);
    }
}