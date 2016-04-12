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

class ComNucleonplusRebatePackagereferral extends KObject
{
    /**
     * Referral bonus controller.
     *
     * @param KObjectIdentifierInterface
     */
    protected $_controller;

    /**
     * Number of levels for direct referrals
     *
     * @param integer
     */
    protected $_unilevel_count;

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

        $this->_controller     = $this->getObject($config->controller);
        $this->_unilevel_count = $config->unilevel_count;

        // Accounting service
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
            'controller'         => 'com:nucleonplus.controller.referralbonuses',
            'accounting_service' => 'com:nucleonplus.accounting.service.transfer',
            'unilevel_count'     => 10
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
    public function create(KModelEntityInterface $reward)
    {
        $account = $this->getObject('com:nucleonplus.model.accounts')->id($reward->customer_id)->fetch();
        $this->_recordReferrals($account, $reward);
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
        $points = ($reward->drpv * $reward->slots);

        if (is_null($account->sponsor_id))
        {
            $this->_accounting_service->allocateSurplusDRBonus($reward->product_id, $points);

            $points = (($reward->irpv * $this->_unilevel_count) * $reward->slots);
            $this->_accounting_service->allocateSurplusIRBonus($reward->product_id, $points);

            return true;
        }

        // Record direct referral
        $data = [
            'reward_id'     => $reward->id,
            'account_id'    => $account->getIdFromSponsor(),
            'referral_type' => 'dr', // Direct Referral
            'points'        => $points,
        ];

        $this->_controller->add($data);

        // Post direct referral to accounting system
        $this->_accounting_service->allocateDRBonus($reward->product_id, $points);

        // Check if direct referrer has sponsor as well
        $directSponsor = $this->getObject('com:nucleonplus.model.accounts')->id($account->getIdFromSponsor())->fetch();

        if (!is_null($directSponsor->sponsor_id))
        {
            $immediateSponsorId = $directSponsor->getIdFromSponsor();
            $this->_recordIndirectReferrals($immediateSponsorId, $reward);
        }
        else
        {
            $this->_accounting_service->allocateSurplusIRBonus($reward->product_id, ($reward->irpv * $this->_unilevel_count));
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

        // Try to get referrers up to the 10th level
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