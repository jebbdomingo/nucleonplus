<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmPackagedirectreferral extends KObject
{
    /**
     * Accounting Service
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_accounting_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $identifier = $this->getIdentifier($config->accounting_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceTransferInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceTransferInterface"
            );
        }
        else $this->_accounting_service = $service;
    }

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
            'accounting_service' => 'com:nucleonplus.accounting.service.transfer'
        ));

        parent::_initialize($config);
    }

    /**
     * Create direct referral bonus
     *
     * @param KModelEntityInterface $slot
     *
     * @return void
     */
    public function create(KModelEntityInterface $slot)
    {
        // Purchaser (new member)
        $account = $slot->getReward()->getAccount();

        // Check if the new member/purchaser has referrer
        // and this is his first purchase
        if ($account->sponsor_id && (count($account->getLatestPurchases()) == 1)) {
            // Pay referrer a direct referrral bonus
            return $this->_connectToSponsor($account->getSponsor(), $slot);
        }

        return false;
    }

    /**
     * Direct referral bonus
     *
     * @param KModelEntityInterface $sponsor
     * @param KModelEntityInterface $slot
     *
     * @return booelan
     */
    private function _connectToSponsor(KModelEntityInterface $sponsor, KModelEntityInterface $slot)
    {
        $reward = $slot->getReward();

        $data = array(
            'reward_id'  => $reward->id,
            'account_id' => $sponsor->id,
            'points'     => $reward->prpv
        );

        $directReferral = $this->getObject('com:nucleonplus.model.directreferrals')->create($data);
        
        if ($directReferral->save())
        {
            $this->_payReferrer($slot);

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
    protected function _payReferrer($slot)
    {
        if ($slot->consume())
        {
            $reward = $slot->getReward();
            $this->_accounting_service->allocateDirectReferral($reward->product_id, $reward->prpv);
        }
    }
}
