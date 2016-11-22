<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmUnilevelpackage extends ComNucleonplusMlmUnilevel
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'unilevel_count' => 20
        ));

        parent::_initialize($config);
    }

    /**
     * Record referral bonus payouts
     *
     * @param KModelEntityInterface $reward
     *
     * @return void
     */
    public function _actionCreate(KModelEntityInterface $reward)
    {
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($reward->customer_id)->fetch();

        // Ensure direct referral point value exists
        if ($reward->drpv > 0) {
            $this->_recordReferrals($account, $reward);
        }
    }

    /**
     * Record direct referrals
     *
     * @param KModelEntityInterface $reward
     *
     * @return void
     */
    private function _recordReferrals(KModelEntityInterface $account, KModelEntityInterface $reward)
    {
        $drPoints = ($reward->drpv * $reward->slots);

        if (is_null($account->sponsor_id))
        {
            $this->_accounting_service->allocateSurplusDRBonus($reward->product_id, $drPoints);

            $irPoints = (($reward->irpv * $this->_unilevel_count) * $reward->slots);
            $this->_accounting_service->allocateSurplusIRBonus($reward->product_id, $irPoints);

            return true;
        }

        // Record direct referral
        $data = [
            'reward_id'     => $reward->id,
            'account_id'    => $account->getIdFromSponsor(),
            'referral_type' => 'dr', // Direct Referral
            'points'        => $drPoints,
        ];

        $this->_controller->add($data);

        // Post direct referral to accounting system
        $this->_accounting_service->allocateDRBonus($reward->product_id, $drPoints);

        // Check if direct referrer has sponsor as well
        $directSponsor = $this->getObject('com:nucleonplus.model.accounts')->id($account->getIdFromSponsor())->fetch();

        if (!is_null($directSponsor->sponsor_id))
        {
            $immediateSponsorId = $directSponsor->getIdFromSponsor();
            $this->_recordIndirectReferrals($immediateSponsorId, $reward);
        }
        else
        {
            $irPoints = (($reward->irpv * $this->_unilevel_count) * $reward->slots);
            $this->_accounting_service->allocateSurplusIRBonus($reward->product_id, $irPoints);
        }
    }

    /**
     * Record indirect referrals
     *
     * @param integer               $id Sponsor/indirect referrer ID
     * @param KModelEntityInterface $reward
     *
     * @return void
     */
    private function _recordIndirectReferrals($id, KModelEntityInterface $reward)
    {
        $points = ($reward->irpv * $reward->slots);
        $x      = 0;

        // Try to get referrers up to the 20th level
        while ($x < $this->_unilevel_count)
        {
            $indirectReferrer = $this->getObject('com:nucleonplus.model.accounts')->id($id)->fetch();

            $data = array(
                'reward_id'     => $reward->id,
                'account_id'    => $indirectReferrer->id,
                'referral_type' => 'ir', // Indirect Referral
                'points'        => $points
            );

            $this->_controller->add($data);
            $this->_accounting_service->allocateIRBonus($reward->product_id, $points);
            
            $x++;

            // Terminate execution if the current indirect referrer has no sponsor/referrer
            // i.e. there are no other indirect referrers to pay
            if (is_null($indirectReferrer->sponsor_id))
            {
                if ($x < $this->_unilevel_count)
                {

                    $points = ($this->_unilevel_count - $x) * $reward->irpv;
                    $this->_accounting_service->allocateSurplusIRBonus($reward->product_id, $points);

                    break;

                }

                break;
            }

            $id = $indirectReferrer->getIdFromSponsor();
        }
    }
}