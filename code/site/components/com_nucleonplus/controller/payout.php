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
        // $this->addCommandCallback('before.add', '_validateData');
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
            $data       = $context->request->data;

            if (!in_array($data->payout_method, array(ComRewardlabsModelEntityPayout::PAYOUT_METHOD_PICKUP, ComRewardlabsModelEntityPayout::PAYOUT_METHOD_FUNDS_TRANSFER))) {
                throw new Exception('Please choose how do you want to encash your commissions');
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
        $account = $this->getObject('com://site/rewardlabs.model.accounts')->user_id($user->getId())->fetch();
        
        $rebateBonus     = 0;
        $unilevelDRBonus = 0;
        $unilevelIRBonus = 0;

        $contextData  = $context->request->data;

        // Ensure there is no discrepancy in member's rebates payout request
        if ($contextData->rebates)
        {
            foreach ($contextData->rebates as $id)
            {
                $rebate = $this->getObject('com://admin/nucleonplus.model.rebates')
                    ->account_id($account->id)
                    ->id($id)
                    ->fetch()
                ;

                if (is_null($rebate->id)) {
                    throw new Exception("There is a discrepancy in your rebates payout request. ref# {$id}");
                }

                $reward = $this->getObject('com://admin/nucleonplus.model.rewards')
                    ->id($rebate->reward_id)
                    ->payout_id(0)
                    ->fetch()
                ;

                // Ensure patronage are paid based on the matched reward/slots
                if ($reward->rebates <> $rebate->points) {
                    throw new Exception("There is a discrepancy in your rebates payout. ref# {$reward->id}-{$rebate->id}");
                }
                else
                {
                    $rebateBonus += $rebate->points;
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

        $total = ($rebateBonus + $unilevelDRBonus + $unilevelIRBonus);

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
        $query = $context->request->query;
        $data  = $context->request->data;

        $data->account = $query->get('account', 'cmd');
        $data->status  = 'pending';

        $payout = parent::_actionAdd($context);

        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=payouts', $identifier->package);

        $response = $context->getResponse();
        $response->addMessage("Your payout request amounting to &#8369; {$payout->amount} has been created successfully");

        $response->setRedirect(JRoute::_($url, false));

        return $payout;
    }
}