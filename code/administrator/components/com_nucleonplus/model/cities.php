<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelCities extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('province_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'citysearchable' => array('columns' => array('_name'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _beforeFetch(KModelContextInterface $context)
    {
        $model    = $context->getSubject();
        $state    = $context->state;
        $query    = $context->query;
        $category = null;

        $query
            ->columns(array('_name'          => "CONCAT(tbl.name, ', ', _province.name)"))
            ->columns(array('_province_name' => '_province.name'))
            ->columns(array('_province_id'   => '_province.nucleonplus_province_id'))
            ->join(array('_province' => 'nucleonplus_provinces'), 'tbl.province_id = _province.nucleonplus_province_id', 'INNER')
        ;
    }

    protected function _beforeCount(KModelContextInterface $context)
    {
        $this->_beforeFetch($context);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->province_id) {
            $query->where('tbl.province_id = :province_id')->bind(['province_id' => $state->province_id]);
        }
    }
}