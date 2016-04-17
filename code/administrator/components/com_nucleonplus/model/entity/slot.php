<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * Slot Entity.
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Component\Nucelonplus
 */
class ComNucleonplusModelEntitySlot extends KModelEntityRow
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
     * Mark this slot as consumed i.e. it is allocated to an upline slot
     *
     * @return boolean|void
     */
    public function consume()
    {
        $this->consumed = 1;
        
        if ($this->save())
        {
            $reward = $this->getReward();
            $this->_accounting_service->allocateRebates($reward->product_id, $reward->prpv);
        }
    }

    /**
     * Flush the slot's prpv out
     * Usually when there's no upline slot available
     *
     * @return [type] [description]
     */
    public function flushOut()
    {
        $reward = $this->getReward();
        $this->_accounting_service->allocateSurplusRebates($reward->product_id, $reward->prpv);
    }

    /**
     * Prevent deletion of slot
     * A slot can only be voided but not deleted
     *
     * @return boolean FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Get the reward
     *
     * @return KModelEntityInterface
     */
    public function getReward()
    {
        return $this->getObject('com:nucleonplus.model.rewards')->id($this->reward_id)->fetch();
    }
}