<?php
/**
 * Nucleon Plus - Admin
 *
 * @package     Nucleon Plus - Admin
 * @copyright   Copyright (C) 2017 Nucleon Plus Co. (http://www.rewardlabs.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/rewardlabs for the canonical source repository
 */

class ComNucleonplusModelBehaviorCitysearchable extends KModelBehaviorSearchable
{
    /**
     * Add search query
     *
     * @param   KModelContextInterface $context A model context object
     *
     * @return    void
     */
    protected function _buildQuery(KModelContextInterface $context)
    {
        $model = $context->getSubject();

        if ($model instanceof KModelDatabase && !$context->state->isUnique())
        {
            $state  = $context->state;
            $search = $state->search;

            if ($search)
            {
                $conditions = array();

                foreach ($this->_columns as $column) {
                    $conditions[] = $column . ' LIKE :search';
                }

                if ($conditions) {
                    $context->query->having('(' . implode(' OR ', $conditions) . ')')
                                   ->bind(array('search' => '%' . $search . '%'));
                }
            }
        }
    }
}