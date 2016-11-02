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
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'type' => ComNucleonplusModelEntityReward::REWARD_PACKAGE
        ));

        parent::_initialize($config);
    }

    protected function _validate(KModelEntityInterface $slot)
    {
        $result = false;

        // Purchaser (new member)
        $account = $slot->getReward()->getAccount();

        $orders = $this->getObject('com:nucleonplus.model.orders')
            ->account_id($account->id)
            ->order_status(array(
                ComNucleonplusModelEntityOrder::STATUS_PROCESSING,
                ComNucleonplusModelEntityOrder::STATUS_COMPLETED,
            ))
            ->fetch()
        ;
        $numOrders = count($orders);

        // Check if the new member/purchaser has referrer
        // and this is his first purchase
        if ($account->sponsor_id && $numOrders == 1) {
            // Pay referrer a direct referrral bonus
            $result = true;
        }

        return $result;
    }

    /**
     * Create direct referral bonus
     *
     * @param KModelEntityInterface $slot
     *
     * @return KModelEntityInterface|boolean
     */
    public function _actionCreate(KModelEntityInterface $slot)
    {
        $result = false;

        if ($this->_validate($slot))
        {
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
                $this->_recordAcctgTransaction($reward);

                $result = $directReferral;
            }
            else $result = false;
        }

        return $result;
    }

    /**
     * Allocate retail direct referral in the accounting book
     *
     * @return void
     */
    protected function _recordAcctgTransaction($reward)
    {
        $this->_accounting_service->allocateDirectReferral($reward->product_id, $reward->prpv);
    }
}
