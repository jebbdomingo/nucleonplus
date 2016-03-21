<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComQbsyncModelItems extends ComQbsyncQuickbooksModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('id', 'int')
        ;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                //'searchable' => array('columns' => array('nucleonplus_order_id', 'package_name', 'account_number', 'invoice_status'))
            )
        ));

        parent::_initialize($config);
    }

    /**
     *
     * @param KModelContext $context A model context object
     * 
     * @return KModelEntityInterface The entity
     */
    protected function _actionFetch(KModelContext $context)
    {
        // Item
        $itemService = new QuickBooks_IPP_Service_Term();

        $items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '{$context->state->id}' ");

        if (count($items) == 0) {
            return null;
        }

        return $items[0];
    }
}