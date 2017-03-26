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

class ComNucleonplusMlmRevenue extends KObject
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
            'accounting_service' => 'com:nucleonplus.accounting.service.transfer',
        ));

        parent::_initialize($config);
    }

    /**
     * Record revenue from order
     *
     * @param KModelEntityInterface $entity
     *
     * @return void
     */
    final public function create(KModelEntityInterface $entity)
    {
        return $this->_actionCreate($entity);
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
        $amount = (float) $reward->profit;
        $this->_accounting_service->allocateRevenue($reward->product_id, $amount);
    }
}