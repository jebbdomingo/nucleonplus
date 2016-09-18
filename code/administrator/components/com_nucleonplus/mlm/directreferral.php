<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusMlmDirectreferral extends KObject
{
    /**
     * Direct referral type e.g. package or retail
     *
     * @var string
     */
    protected $_type;

    /**
     * Accounting Service
     *
     * @var ComNucleonplusAccountingServiceTransferInterface
     */
    protected $_accounting_service;

    /**
     * Constructor
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Mixin the behavior interface
        $this->mixin('lib:behavior.mixin', $config);

        // Mixin the event interface
        $this->mixin('lib:event.mixin', $config);

        $this->_type = $config->type;

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
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'accounting_service' => 'com:nucleonplus.accounting.service.transfer',
            'type'               => null,
            'command_chain'      => 'lib:command.chain',
            'command_handlers'   => array('lib:command.handler.event'),
        ));

        parent::_initialize($config);
    }

    /**
     * Implement create()
     *
     * @param KModelEntityInterface $entity
     *
     * @return void
     */
    final public function create(KModelEntityInterface $entity)
    {
        $context = $this->getContext();
        $context->entity = $entity;

        if ($this->invokeCommand('before.create', $context) !== false)
        {
            $this->_actionCreate($context);
            $this->invokeCommand('after.create', $context);
        }
    }

    /**
     * Get the model context
     *
     * @return  KModelContext
     */
    public function getContext()
    {
        $context = new KModelContext();
        $context->setSubject($this);

        return $context;
    }
}
