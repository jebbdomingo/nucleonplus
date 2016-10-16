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
 * Reward Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComNucleonplusControllerReward extends ComKoowaControllerModel
{
    /**
     *
     * @var ComNucleonplusMlmCompensation
     */
    private $_compensation_package;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_compensation_package = $this->getObject($config->compensation_package);
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
            'compensation_package' => 'com:nucleonplus.mlm.compensation',
        ));

        parent::_initialize($config);
    }

    /**
     * Activates the reward and create corresponding slots
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionActivate(KControllerContextInterface $context)
    {
        @ini_set('max_execution_time', 300);

        if (!$context->result instanceof KModelEntityInterface) {
            $rewards = $this->getModel()->fetch();
        } else {
            $rewards = $context->result;
        }

        if (count($rewards))
        {
            $translator = $this->getObject('translator');

            foreach ($rewards as $reward)
            {
                switch ($reward->status) {
                    case 'active':
                        throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Request: Reward #{$reward->id} is already active"));
                        $result = false;
                        break;
                    
                    case 'ready':
                        throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Request: Reward #{$reward->id} is ready for payout"));
                        $result = false;
                        break;

                    case 'claimed':
                        throw new KControllerExceptionRequestInvalid($translator->translate("Invalid Request: Reward #{$reward->id} is already claimed"));
                        $result = false;
                        break;
                }

                // Create compensations
                $this->_compensation_package->create($reward);
            }
        }
        else throw new KControllerExceptionResourceNotFound('Resource could not be found');

        return $rewards;
    }
}