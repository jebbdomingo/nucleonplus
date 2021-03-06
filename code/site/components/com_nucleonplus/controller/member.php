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
 * Member Controller
 *
 * @author  Jebb Domingo <http://github.com/jebbdomingo>
 * @package Nucleon Plus
 */
class ComNucleonplusControllerMember extends ComKoowaControllerModel
{
    /**
     * Constructor
     *
     * @param KObjectConfig $config
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.edit', '_validateSponsorId');
    }

    /**
     * Validate sponsor id
     *
     * @param KControllerContextInterface $context
     * 
     * @return KModelEntityInterface
     */
    protected function _validateSponsorId(KControllerContextInterface $context)
    {
        $result = true;

        if (!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        if (count($entities))
        {
            foreach($entities as $entity) {
                $entity->setProperties($context->request->data->toArray());
            }

            try
            {
                $translator = $this->getObject('translator');

                $sponsorId = trim($entity->sponsor_id);

                if (!empty($sponsorId))
                {
                    $account = $this->getObject('com:nucleonplus.model.accounts')->account_number($sponsorId)->fetch();

                    if (count($account) == 0)
                    {
                        throw new KControllerExceptionRequestInvalid($translator->translate('Invalid Sponsor ID'));
                        $result = false;
                    }
                }
            }
            catch(Exception $e)
            {
                $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $e->getMessage(), 'error');
                $context->getResponse()->send();

                $result = false;
            }
        }
        else throw new KControllerExceptionResourceNotFound('Resource Not Found');

        return $result;
    }
}