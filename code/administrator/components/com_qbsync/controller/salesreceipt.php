<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComQbsyncControllerSalesreceipt extends ComKoowaControllerModel
{
    /**
     * Undeposited funds account
     *
     * @var integer
     */
    protected $_undeposited_funds_account;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_undeposited_funds_account = $config->undeposited_funds_account;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'undeposited_funds_account' => 182,
        ));

        parent::_initialize($config);
    }

    /**
     * Sync Action
     *
     * @param   KControllerContextInterface $context A command context object
     * @throws  KControllerExceptionRequestNotAuthorized If the user is not authorized to update the resource
     * 
     * @return  KModelEntityInterface
     */
    protected function _actionSync(KControllerContextInterface $context)
    {
        if(!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        if(count($entities))
        {
            foreach($entities as $entity)
            {
                $entity->setProperties($context->request->data->toArray());

                if ($entity->sync() === false)
                {
                    $error = $entity->getStatusMessage();
                    $context->response->addMessage($error ? $error : 'Sync Action Failed', 'error');
                }
                else $context->response->setStatus(KHttpResponse::NO_CONTENT);
            }
        }
        else throw new KControllerExceptionResourceNotFound('Resource Not Found');

        return $entities;
    }

    /**
     * Add
     *
     * @param KControllerContextInterface $context
     *
     * @return KModelEntityInterface
     */
    protected function _actionAdd(KControllerContextInterface $context)
    {
        if (is_null($context->request->data->DepositToAccountRef)) {
            $context->request->data->DepositToAccountRef = $this->_undeposited_funds_account;
        }

        return parent::_actionAdd($context);
    }
}