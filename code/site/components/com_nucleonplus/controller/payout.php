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

        $this->addCommandCallback('before.add', '_checkClaim');
        $this->addCommandCallback('before.add', '_validateData');
    }

    /**
     * Check if claiming is enabled
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _checkClaim(KControllerContextInterface $context)
    {
        try
        {
            $translator = $this->getObject('translator');

            $claimRequest = $this->getObject('com:nucleonplus.model.configs')->item('claim_request')->fetch();

            if ($claimRequest->value == 'no') {
                throw new Exception('Claim request is not available at the moment, please check the cut-off time for claim requests');
            }

            $data = $context->request->data;

            if (!in_array($data->payout_method, array('pickup', 'deposit'))) {
                throw new Exception('Please choose how do you want to encash your commission/referral fee');
            }

            // For deposit, ensure customer has bank account details
            if ($data->payout_method == 'deposit')
            {
                $user    = $this->getObject('user');
                $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user->getId())->fetch();

                $acctNumber = trim($account->bank_account_number);
                $acctName   = trim($account->bank_account_name);
                $acctType   = trim($account->bank_account_type);

                if (empty($acctNumber) || empty($acctName) || empty($acctType)) {
                    throw new Exception('Please complete your bank account details in your profile');
                }
            }
        }
        catch(Exception $e)
        {
            $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
            $context->getResponse()->send();

            $result = false;
        }
    }
    /**
     * Validate and construct data
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateData(KControllerContextInterface $context)
    {
        $user    = $this->getObject('user');
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')->user_id($user->getId())->fetch();
        
        $directReferralBonus = 0;
        $patronageBonus      = 0;
        $unilevelDRBonus     = 0;
        $unilevelIRBonus     = 0;

        $contextData  = $context->request->data;

        // Ensure there is no discrepancy in member's direct referral bonus payout
        if ($contextData->direct_referrals)
        {
            foreach ($contextData->direct_referrals as $id)
            {
                $directReferral = $this->getObject('com://admin/nucleonplus.model.directreferrals')
                    ->account_id($account->id)
                    ->id($id)
                    ->fetch()
                ;

                if (is_null($directReferral->id)) {
                    throw new Exception("There is a discrepancy in your direct referral payout request. ref# {$id}");
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($directReferral->reward_id)
                    ->payout_id(0)
                    ->fetch()
                ;

                // Ensure patronage are paid based on the matched reward/slots
                if ($rewardFrom->prpv <> $directReferral->points) {
                    throw new Exception("There is a discrepancy in your direct referral payout. ref# {$rewardFrom->id}-{$directReferral->id}");
                }
                else
                {
                    $directReferralBonus += $directReferral->points;
                    $redeemedDRBonus[]   = $directReferral->id;
                }
            }
        }

        // Ensure there is no discrepancy in member's requested payout in his patronages
        if ($contextData->patronages)
        {
            foreach ($contextData->patronages as $id)
            {
                $patronage = $this->getObject('com://admin/nucleonplus.model.patronagebonus')
                    ->customer_id($account->id)
                    ->id($id)
                    ->fetch()
                ;

                if (is_null($patronage->id)) {
                    throw new Exception("There is a discrepancy in your patronage bonus payout request. ref# {$id}");
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($patronage->reward_id_from)
                    ->status(array('active', 'ready'))
                    ->fetch()
                ;

                // Ensure patronage are paid based on the matched reward/slots
                if ($rewardFrom->prpv <> $patronage->points) {
                    throw new Exception("There is a discrepancy in your patronage bonus. ref# {$rewardFrom->id}-{$patronage->id}");
                }
                else
                {
                    $patronageBonus += $patronage->points;
                    $redeemedPatronageBonus[] = $patronage->id;
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

                if (is_null($referral->id)) {
                    throw new Exception("There is a discrepancy in your unilevel direct referral payout request. ref# {$id}");
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($referral->reward_id)
                    ->fetch()
                ;

                // Ensure referral bonus are paid based on the paying reward
                if (($rewardFrom->drpv * $rewardFrom->slots) <> $referral->points) {
                    throw new Exception("There is a discrepancy in your unilevel direct referral bonus. ref# {$rewardFrom->id}-{$referral->id}");
                }
                else
                {
                    $unilevelDRBonus           += $referral->points;
                    $redeemedUnilevelDRBonus[] = $referral->id;
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

                if (is_null($referral->id)) {
                    throw new Exception("There is a discrepancy in your unilevel indirect referral payout request. ref# {$id}");
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($referral->reward_id)
                    ->payout_id(0)
                    ->fetch()
                ;

                // Ensure referral bonus is paid based on the paying reward
                if (($rewardFrom->irpv * $rewardFrom->slots) <> $referral->points) {
                    throw new Exception("There is a discrepancy in your unilevel indirect referral bonus. ref# {$rewardFrom->id}-{$referral->id}");
                }
                else
                {
                    $unilevelIRBonus           += $referral->points;
                    $redeemedUnilevelIRBonus[] = $referral->id;
                }
            }
        }

        $total = ($unilevelDRBonus + $unilevelIRBonus + $patronageBonus + $directReferralBonus);

        $data = new KObjectConfig([
            'account_id'       => $account->id,
            'amount'           => $total,
            'status'           => 'pending',
            'payout_method'    => $contextData->payout_method,
            'redeemed_drbonus' => $redeemedDRBonus,
            'redeemed_pr'      => $redeemedPatronageBonus,
            'redeemed_dr'      => $redeemedUnilevelDRBonus,
            'redeemed_ir'      => $redeemedUnilevelIRBonus,
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
        $commission = parent::_actionAdd($context);

        $directReferralBonus = $context->request->data->redeemed_drbonus;
        $patronages             = $context->request->data->redeemed_pr;
        $directReferrals     = $context->request->data->redeemed_dr;
        $indirectReferrals   = $context->request->data->redeemed_ir;
        $rewards             = array();

        // Patronage payout request processing
        foreach ($directReferralBonus as $id)
        {
            $referral            = $this->getObject('com:nucleonplus.model.directreferrals')->id($id)->fetch();
            $referral->payout_id = $commission->id;
            $referral->save();
        }

        // Patronage payout request processing
        foreach ($patronages as $id)
        {
            $patronage = $this->getObject('com:nucleonplus.model.patronagebonus')->id($id)->fetch();
            $patronage->payout_id = $commission->id;
            $patronage->save();

            $rewards[] = $patronage->reward_id_to;
        }

        $rewards = array_unique($rewards);

        // Update status of the rewards claimed
        foreach ($rewards as $id)
        {
            $reward            = $this->getObject('com:nucleonplus.model.rewards')->id($id)->fetch();
            $reward->payout_id = $commission->id;
            $reward->status    = 'processing';
            $reward->save();
        }

        // Reference related direct referrals with payout
        foreach ($directReferrals as $id)
        {
            $referral            = $this->getObject('com:nucleonplus.model.referralbonuses')->id($id)->fetch();
            $referral->payout_id = $commission->id;
            $referral->save();
        }

        // Reference related indirect referrals with payout
        foreach ($indirectReferrals as $id)
        {
            $referral            = $this->getObject('com:nucleonplus.model.referralbonuses')->id($id)->fetch();
            $referral->payout_id = $commission->id;
            $referral->save();
        }

        return $commission;
    }
}