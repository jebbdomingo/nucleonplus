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
        
        $totalPr = 0;
        $totalDr = 0;
        $totalIr = 0;

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

                if (is_null($rebate->id)) {
                    throw new Exception("There is a discrepancy in your rebates payout request. ref# {$id}");
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($rebate->reward_id_from)
                    ->status('ready')
                    ->fetch()
                ;

                // Ensure rebates are paid based on the matched reward/slots
                if ($rewardFrom->prpv <> $rebate->points) {
                    throw new Exception("There is a discrepancy in your rewards. ref# {$rewardFrom->id}-{$rebate->id}");
                }
                else
                {
                    $totalPr += $rebate->points;
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

                if (is_null($referral->id)) {
                    throw new Exception("There is a discrepancy in your direct referral payout request. ref# {$id}");
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($referral->reward_id)
                    ->fetch()
                ;

                // Ensure referral bonus are paid based on the paying reward
                if (($rewardFrom->drpv * $rewardFrom->slots) <> $referral->points) {
                    throw new Exception("There is a discrepancy in your direct referral bonus. ref# {$rewardFrom->id}-{$referral->id}");
                }
                else
                {
                    $totalDr      += $referral->points;
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

                if (is_null($referral->id)) {
                    throw new Exception("There is a discrepancy in your indirect referral payout request. ref# {$id}");
                }

                $rewardFrom = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($referral->reward_id)
                    ->payout_id(0)
                    ->fetch()
                ;

                // Ensure referral bonus is paid based on the paying reward
                if (($rewardFrom->irpv * $rewardFrom->slots) <> $referral->points) {
                    throw new Exception("There is a discrepancy in your indirect referral bonus. ref# {$rewardFrom->id}-{$referral->id}");
                }
                else
                {
                    $totalIr      += $referral->points;
                    $redeemedIr[] = $referral->id;
                }
            }
        }

        $total = ($totalDr + $totalIr + $totalPr);

        $data = new KObjectConfig([
            'account_id'    => $account->id,
            'amount'        => $total,
            'status'        => 'pending',
            'payout_method' => $contextData->payout_method,
            'redeemed_pr'   => $redeemedPr,
            'redeemed_dr'   => $redeemedDr,
            'redeemed_ir'   => $redeemedIr,
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

        $rebates           = $context->request->data->redeemed_pr;
        $directReferrals   = $context->request->data->redeemed_dr;
        $indirectReferrals = $context->request->data->redeemed_ir;
        $rewards           = array();

        // Rebates payout request processing
        foreach ($rebates as $id)
        {
            $rebate = $this->getObject('com:nucleonplus.model.rebates')->id($id)->fetch();
            $rebate->payout_id = $commission->id;
            $rebate->save();

            $rewards[] = $rebate->reward_id_to;
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