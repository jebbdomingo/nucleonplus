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
 * Account Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComNucleonplusControllerAccount extends ComKoowaControllerModel
{
    /**
     *
     * @var ComNucleonplusAccountingServiceMemberInterface
     */
    protected $_member_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $identifier = $this->getIdentifier($config->member_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceMemberInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceMemberInterface"
            );
        }
        else $this->_member_service = $service;
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
            'member_service' => 'com:nucleonplus.accounting.service.member',
        ));

        parent::_initialize($config);
    }

    /**
     * Specialized save action, changing state by terminating
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionTerminate(KControllerContextInterface $context)
    {
        $context->getRequest()->setData(['status' => 'terminated']);

        parent::_actionEdit($context);
    }

    /**
     * Specialized save action, changing state by activating
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     *
     * @return KModelEntityInterface
     */
    protected function _actionActivate(KControllerContextInterface $context)
    {
        if(!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        foreach ($entities as $entity)
        {
            if ($entity->status == 'pending')
            {
                $customer = $this->_member_service->pushMember($entity);
                
                if ($customer->sync() === false)
                {
                    $error = $entities->getStatusMessage();
                    throw new KControllerExceptionActionFailed($error ? $error : "Sync Error: Account #{$entity->account_number}");
                }
                else
                {
                    $entity->status      = 'active';
                    $entity->CustomerRef = $customer->CustomerRef;
                    $entity->save();
                    
                    // Send email notification
                    $config = JFactory::getConfig();

                    $emailSubject = "Your Nucleon Plus Account has been activated";
                    $emailBody    = JText::sprintf(
                        'COM_NUCLEONPLUS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
                        $entity->_name,
                        JUri::root()
                    );

                    $return = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $entity->_email, $emailSubject, $emailBody);

                    // Check for an error.
                    if ($return !== true)
                    {
                        $context->response->addMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'), 'error');
                    }

                    $context->response->addMessage("Account #{$entity->account_number} has been activated");
                }
            }
            else $context->response->addMessage("Unable to activate Account #{$entity->account_number}, only pending accounts can be activated", 'warning');
        }

        return $entities;
    }
}