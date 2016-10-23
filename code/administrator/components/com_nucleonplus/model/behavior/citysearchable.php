<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelBehaviorCitysearchable extends KModelBehaviorSearchable
{
    protected function _beforeFetch(KModelContextInterface $context)
    {
        $model    = $context->getSubject();
        $state    = $context->state;
        $query    = $context->query;
        $category = null;

        $query
            ->columns(array('_name' => "CONCAT(tbl.name, ', ', _province.name)"))
            ->join(array('_province' => 'nucleonplus_provinces'), 'tbl.province_id = _province.nucleonplus_province_id', 'INNER')
        ;
    }

    protected function _beforeCount(KModelContextInterface $context)
    {
        $this->_beforeFetch($context);
    }

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