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
        $user    = $this->getObject('user');
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user->getId())->fetch();
        
        $totalPr      = 0;
        $totalDrBonus = 0;
        $totalIrBonus = 0;

        $contextData  = $context->request->data;

        // Ensure there is no discrepancy in member's requested payout in his rebates
        if ($contextData->rebates)
        {
            foreach ($contextData->rebates as $id)
            {
                $rebate = $this->getObject('com://admin/nucleonplus.model.rebates')
                    ->customer_id($account->id)
                    ->id($id)
                    ->fetch()
                ;

                if (is_null($rebate->id))
                {
                    throw new Exception("There is a discrepancy in your rebates payout request. ref# {$id}");

                    return false;
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($rebate->reward_id_from)
                    ->fetch()
                ;

                // Ensure rebates are paid based on the matched reward/slots
                if ($rewardFrom->prpv <> $rebate->points) {
                    throw new Exception("There is a discrepancy in your rewards. ref# {$rewardFrom->id}-{$rebate->id}");

                    return false;
                }
                else
                {
                    $totalPr += $rewardFrom->prpv;
                    $redeemedPr[] = $rebate->id;
                }
            }
        }

        // Ensure there is no discrepancy in member's requested payout in his direct referral bonuses
        if ($contextData->dr_bonuses)
        {
            foreach ($contextData->dr_bonuses as $id)
            {
                $referral = $this->getObject('com://admin/nucleonplus.model.referralbonuses')
                    ->id($id)
                    ->account_id($account->id)
                    ->referral_type('dr')
                    ->payout_id(0)
                    ->fetch()
                ;

                if (is_null($referral->id))
                {
                    throw new Exception("There is a discrepancy in your direct referral payout request. ref# {$id}");

                    return false;
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($referral->reward_id)
                    ->fetch()
                ;

                // Ensure referral bonus are paid based on the paying reward
                if (($rewardFrom->drpv * $rewardFrom->slots) <> $referral->points) {
                    throw new Exception("There is a discrepancy in your direct referral bonus. ref# {$rewardFrom->id}-{$referral->id}");
                    return false;
                }
                else
                {
                    $totalDr      += $rewardFrom->drpv;
                    $redeemedDr[] = $referral->id;
                }
            }
        }

        // Ensure there is no discrepancy in member's requested payout in his indirect referral bonuses
        if ($contextData->ir_bonuses)
        {
            foreach ($contextData->ir_bonuses as $id)
            {
                $referral = $this->getObject('com://admin/nucleonplus.model.referralbonuses')
                    ->id($id)
                    ->account_id($account->id)
                    ->referral_type('ir')
                    ->payout_id(0)
                    ->fetch()
                ;

                if (is_null($referral->id))
                {
                    throw new Exception("There is a discrepancy in your indirect referral payout request. ref# {$id}");

                    return false;
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($referral->reward_id)
                    ->payout_id(0)
                    ->fetch()
                ;

                // Ensure referral bonus is paid based on the paying reward
                if (($rewardFrom->irpv * $rewardFrom->slots) <> $referral->points) {
                    throw new Exception("There is a discrepancy in your indirect referral bonus. ref# {$rewardFrom->id}-{$referral->id}");
                    return false;
                }
                else
                {
                    $totalIr      += $rewardFrom->irpv;
                    $redeemedIr[] = $referral->id;
                }
            }
        }

        $data = new KObjectConfig([
            'account_id'  => $account->id,
            'amount'      => $totalDr + $totalIr + $totalPr,
            'status'      => 'pending',
            'redeemed_pr' => $redeemedPr,
            'redeemed_dr' => $redeemedDr,
            'redeemed_ir' => $redeemedIr,
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
    protected function _actionAdd(KControllerContextInterface $context)
    {
        $entity = parent::_actionAdd($context);

        $rebates           = $context->request->data->redeemed_pr;
        $directReferrals   = $context->request->data->redeemed_dr;
        $indirectReferrals = $context->request->data->redeemed_ir;
        $rewards = [];

        // Rebates payout request processing
        foreach ($rebates as $id)
        {
            $rebate = $this->getObject('com:nucleonplus.model.rebates')->id($id)->fetch();
            $rebate->save();

            $rewards[] = $rebate->reward_id_to;
        }

        $rewards = array_unique($rewards);

        // Update status of the rewards/rebates claimed
        foreach ($rewards as $id)
        {
            $reward = $this->getObject('com:nucleonplus.model.rewards')->id($id)->fetch();
            $reward->payout_id = $entity->id;
            $reward->status = 'processing';
            $reward->save();
        }

        // Update direct referral bonus status
        foreach ($directReferrals as $id)
        {
            $referral = $this->getObject('com:nucleonplus.model.referralbonuses')->id($id)->fetch();
            $referral->payout_id = $entity->id;
            $referral->save();
        }

        // Update indirect referral bonus status
        foreach ($indirectReferrals as $id)
        {
            $referral = $this->getObject('com:nucleonplus.model.referralbonuses')->id($id)->fetch();
            $referral->payout_id = $entity->id;
            $referral->save();
        }

        return $entity;
    }
}