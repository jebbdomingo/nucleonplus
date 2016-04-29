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
 * Employee Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComNucleonplusControllerEmployeeaccount extends ComKoowaControllerModel
{
    /**
     *
     * @var ComNucleonplusAccountingServiceEmployeeInterface
     */
    protected $_employee_service;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $identifier = $this->getIdentifier($config->employee_service);
        $service    = $this->getObject($identifier);

        if (!($service instanceof ComNucleonplusAccountingServiceEmployeeInterface))
        {
            throw new UnexpectedValueException(
                "Service $identifier does not implement ComNucleonplusAccountingServiceEmployeeInterface"
            );
        }
        else $this->_employee_service = $service;
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
            'employee_service' => 'com:nucleonplus.accounting.service.employee',
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
            $accounts = $this->getModel()->fetch();
        } else {
            $accounts = $context->result;
        }

        foreach ($accounts as $account)
        {
            if ($account->status == 'pending')
            {
                $employee = $this->_employee_service->pushEmployee($account);
                
                if ($employee->sync() == false)
                {
                    $error = $employee->getStatusMessage();
                    throw new KControllerExceptionActionFailed($error ? $error : "Sync Error: Employee #{$account->id}");
                }
                else
                {
                    $account->EmployeeRef = $employee->EmployeeRef;
                    $account->activate();
                    $account->save();
                    
                    // Send email notification
                    $config = JFactory::getConfig();

                    $emailSubject = "Your Nucleon Plus Account has been activated";
                    $emailBody    = JText::sprintf(
                        'COM_NUCLEONPLUS_EMPLOYEE_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
                        $account->_user_name
                    );

                    $return = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $account->_user_email, $emailSubject, $emailBody);

                    // Check for an error.
                    if ($return !== true)
                    {
                        $context->response->addMessage(JText::_('COM_NUCLEONPLUS_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'), 'error');
                    }

                    $context->response->addMessage("Employee #{$account->id} has been activated");
                }
            }
            else $context->response->addMessage("Unable to activate Employee #{$account->id}, only pending accounts can be activated", 'warning');
        }

        return $accounts;
    }
}