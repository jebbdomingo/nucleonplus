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

class ComNucleonplusMlmUnilevelretail extends ComNucleonplusMlmUnilevel
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

        // Unilevel retail offers indirect referral bonus only
        // Ensure indirect referral point value exists
        if ($reward->irpv > 0) {
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
        $result = false;

        if (is_null($account->sponsor_id))
        {
            // No direct referrer sponsor, flushout indirect referral bonus
            $irPoints = (($reward->irpv * $this->_unilevel_count) * $reward->slots);
            $this->_accounting_service->allocateSurplusIRBonus($reward->product_id, $irPoints);
        }
        else
        {
            // Check if direct referrer has sponsor
            $directSponsor = $this->getObject('com:nucleonplus.model.accounts')->id($account->getIdFromSponsor())->fetch();

            if (!is_null($directSponsor->sponsor_id))
            {
                $immediateSponsorId = $directSponsor->getIdFromSponsor();
                $this->_recordIndirectReferrals($immediateSponsorId, $reward);
            }
            else
            {
                // There's direct referrer sponsor but no indirect referrer sponsor, flushout indirect referral bonus
                $irPoints = (($reward->irpv * $this->_unilevel_count) * $reward->slots);
                $this->_accounting_service->allocateSurplusIRBonus($reward->product_id, $irPoints);
            }
        }

        return $result;
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

        // Try to get referrers up to the _unilevel_count level
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