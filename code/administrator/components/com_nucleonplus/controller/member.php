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

        $this->addCommandCallback('before.add', '_validateSponsorId');
        $this->addCommandCallback('before.save', '_validateSponsorId');
        $this->addCommandCallback('after.save',   '_setRedirect');
        $this->addCommandCallback('after.apply',  '_setRedirect');
    }

    /**
     * Always redirect back to account view
     *
     * @param KControllerContextInterface $context
     */
    protected function _setRedirect(KControllerContextInterface $context)
    {
        $entity   = $context->result;
        $response = $context->getResponse();

        $identifier = $context->getSubject()->getIdentifier();
        $url        = sprintf('index.php?option=com_%s&view=account&id=%d', $identifier->package, $entity->account_id);

        $response->setRedirect($url);
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

        try
        {
            $translator = $this->getObject('translator');

            foreach($entities as $entity) {
                $entity->setProperties($context->request->data->toArray());
            }

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

        return $result;
    }
}