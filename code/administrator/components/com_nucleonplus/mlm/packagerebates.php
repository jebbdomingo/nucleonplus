<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmPackagerebates extends KObject
{
    /**
     * Rebates controller.
     *
     * @param KObjectIdentifierInterface
     */
    protected $_model;

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

        $this->_model = $this->getObject($config->model);

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
            'model'              => 'com:nucleonplus.model.rebates',
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
    public function create(KModelEntityInterface $reward)
    {
        return $this->_createRebates($reward);
    }

    /**
     * Direct referral bonus
     *
     * @param KModelEntityInterface $sponsor
     * @param KModelEntityInterface $slot
     *
     * @return booelan
     */
    private function _createRebates(KModelEntityInterface $reward)
    {
        $points  = $reward->type == ComNucleonplusModelEntityReward::REWARD_PACKAGE ? ($reward->rebates * $reward->slots) : $reward->rebates;
        $rebates = $this->_model->create(array(
            'reward_id'  => $reward->id,
            'account_id' => $reward->getAccount()->id,
            'points'     => $points
        ));
        
        $result = $rebates->save();

        if ($result) {
            $this->_accounting_service->allocateRebates($reward->product_id, $points);
        }

        return $result;
    }
}
