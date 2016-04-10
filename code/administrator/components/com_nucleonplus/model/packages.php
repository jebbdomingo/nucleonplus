<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusModelPackages extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('rewardpackage_id', 'int')
            ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('name', 'rewardpackage_id'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query
            ->columns(array('_rewardpackage_name' => '_rewardpackages.name'))
            ->columns(array('_rewardpackage_slots' => '_rewardpackages.slots'))
            ->columns(array('_rewardpackage_prpv' => '_rewardpackages.prpv'))
            ->columns(array('_rewardpackage_drpv' => '_rewardpackages.drpv'))
            ->columns(array('_rewardpackage_irpv' => '_rewardpackages.irpv'))
            ->columns(array('_qbopackage_itemref' => '_qbopackages.ItemRef'))
            ->columns(array('_qbopackage_unitprice' => '_qbopackages.UnitPrice'))
            ->columns(array('_qbopackage_itemref2' => '_qbopackages.ItemRef2'))
            ->columns(array('_qbopackage_unitprice2' => '_qbopackages.UnitPrice2'))
        ;
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->rewardpackage_id) {
            $query->where('tbl._rewardpackage_id = :rewardpackage_id')->bind(['rewardpackage_id' => $state->rewardpackage_id]);
        }
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        $query
            ->join(array('_rewardpackages' => 'nucleonplus_rewardpackages'), 'tbl.rewardpackage_id = _rewardpackages.nucleonplus_rewardpackage_id')
            ->join(array('_qbopackages' => 'nucleonplus_qbopackages'), 'tbl.nucleonplus_package_id = _qbopackages.package_id')
        ;

        parent::_buildQueryJoins($query);
    }
}