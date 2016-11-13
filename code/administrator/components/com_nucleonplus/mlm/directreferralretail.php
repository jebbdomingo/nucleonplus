<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmDirectreferralretail extends ComNucleonplusMlmDirectreferral
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'type' => ComNucleonplusModelEntityReward::REWARD_RETAIL
        ));

        parent::_initialize($config);
    }

    /**
     * Create direct referral bonus
     *
     * @param KModelEntityInterface $reward
     *
     * @return KModelEntityInterface|boolean
     */
    public function _actionCreate(KModelEntityInterface $reward)
    {
        $result  = false;
        $account = $reward->getAccount();

        $data = array(
            'type'       => $this->_type,
            'reward_id'  => $reward->id,
            'account_id' => $account->getSponsor()->id,
            'points'     => $reward->prpv
        );

        $directReferral = $this->getObject('com:nucleonplus.model.directreferrals')->create($data);
        
        if ($directReferral->save())
        {
            $this->_recordAcctgTransaction($reward);

            $result = $directReferral;
        }
        else $result = false;

        return $result;
    }

    /**
     * Allocate retail direct referral in the accounting book
     *
     * @return void
     */
    protected function _recordAcctgTransaction($reward)
    {
        $this->_accounting_service->allocateDRBonus($reward->product_id, $reward->prpv);
    }
}
