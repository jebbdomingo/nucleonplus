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
 * Rewardable Model Behavior
 *
 * @author  Jebb Domingo <https://github.com/jebbdomingo>
 * @package Nucleonplus\Database\Behavior
 */
class ComNucleonplusModelBehaviorRewardable extends KModelBehaviorAbstract
{
    /**
     * Insert the model states
     *
     * @param KObjectMixable $mixer
     */
    public function onMixin(KObjectMixable $mixer)
    {
        parent::onMixin($mixer);

        // Insert the tag model state
        $mixer->getState()->insert('reward_type', 'string');
    }

    protected function _beforeFetch(KModelContextInterface $context)
    {
        $state = $context->state;
        $query = $context->query;

        if ($state->reward_type)
        {
            $query
                ->where('_reward.type = :reward_type')
                ->bind(array('reward_type' => $state->reward_type))
            ;
        }
    }

    protected function _beforeCount(KModelContextInterface $context)
    {
        $this->_beforeFetch($context);
    }
}