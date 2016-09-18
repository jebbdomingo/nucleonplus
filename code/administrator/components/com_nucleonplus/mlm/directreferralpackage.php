<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmDirectreferralpackage extends ComNucleonplusMlmDirectreferral
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.create', '_validate');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'type' => ComNucleonplusModelEntityReward::REWARD_PACKAGE
        ));

        parent::_initialize($config);
    }

    public function _validate(KModelContext $context)
    {
        // Purchaser (new member)
        $account = $context->entity->getReward()->getAccount();

        // Check if the new member/purchaser has referrer
        // and this is his first purchase
        if ($account->sponsor_id && (count($account->getLatestPurchases()) == 1)) {
            // Pay referrer a direct referrral bonus
            return true;
        }

        return false;
    }

    /**
     * Create direct referral bonus
     *
     * @param KModelContext $slot
     *
     * @return void
     */
    public function _actionCreate(KModelContext $context)
    {
        $slot    = $context->entity;
        $reward  = $slot->getReward();
        $account = $slot->getReward()->getAccount();

        $data = array(
            'type'       => $this->_type,
            'reward_id'  => $reward->id,
            'account_id' => $account->getSponsor()->id,
            'points'     => $reward->prpv
        );

        $directReferral = $this->getObject('com:nucleonplus.model.directreferrals')->create($data);
        
        if ($directReferral->save() && $slot->consume())
        {
            $this->_recordAcctgTransaction($slot);

            return true;
        }
        else return false;
    }

    /**
     * Pay the referrer from slot
     * Mark the slot as consumed i.e. it is allocated to an upline slot
     *
     * @return void
     */
    protected function _recordAcctgTransaction($reward)
    {
        $this->_accounting_service->allocateDirectReferral($reward->product_id, $reward->prpv);
    }
}
