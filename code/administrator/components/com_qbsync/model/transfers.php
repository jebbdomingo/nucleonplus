<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComQbsyncModelTransfers extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('synced', 'string', 'no')
            ->insert('order_id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'searchable' => array('columns' => array('PrivateNote'))
            )
        ));

        parent::_initialize($config);
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        switch ($state->synced) {
            case 'yes':
                $synced = 1;
                break;

            case 'no':
                $synced = 0;
            
            default:
                $synced = 0;
                break;
        }

    
        if ($state->synced <> 'all') {
            $query->where('tbl.synced = :synced')->bind(['synced' => $synced]);
        }

        if ($state->order_id) {
            $query->where('tbl.order_id = :order_id')->bind(['order_id' => $state->order_id]);
        }
    }
}