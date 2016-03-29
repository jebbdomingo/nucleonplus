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
 * Rebate Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComNucleonplusControllerReward extends ComKoowaControllerModel
{
    /**
     * Rebate Package.
     *
     * @var ComNucleonplusRebatePackagerebate
     */
    private $_rebate_package;

    /**
     * Referral Package.
     *
     * @var ComNucleonplusRebatePackagereferral
     */
    private $_referral_package;

    /**
     * Order Model Identifier
     *
     * @var ComKoowaControllerModel
     */
    private $_order_identifier;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_rebate_package   = $config->rebate_package;
        $this->_referral_package = $config->referral_package;
        $this->_order_identifier = $config->order_identifier;
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
            'rebate_package'   => 'com:nucleonplus.rebate.packagerebate',
            'referral_package' => 'com:nucleonplus.rebate.packagereferral',
            'order_identifier' => 'com:nucleonplus.model.orders'
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
        if (!$context->result instanceof KModelEntityInterface) {
            $rewards = $this->getModel()->fetch();
        } else {
            $rewards = $context->result;
        }

        try {
            $rebatePackage   = $this->getObject($this->_rebate_package);
            $referralPackage = $this->getObject($this->_referral_package);

            foreach ($rewards as $reward)
            {
                $orders = $this->getObject($this->_order_identifier)->id($reward->product_id)->fetch();

                foreach ($orders as $order)
                {
                    // Create corresponding slots for this order reward
                    $rebatePackage->create($order);

                    // Create referral bonus payouts
                    $referralPackage->create($order);
                }
            }
        } catch (Exception $e) {
            $identifier = $this->getIdentifier();
            $url        = sprintf('index.php?option=com_%s&view=order&id=%d', $identifier->package, $this->getRequest()->query->id);

            return JFactory::getApplication()->redirect($url, $e->getMessage(), 'exception');
        }

        // Redirect

        return $rewards;
    }
}