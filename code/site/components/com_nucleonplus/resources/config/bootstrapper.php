<?php

return array(
    'aliases' => array(
        // 'com:qbsync.controller.salesreceiptline'                => 'com://admin/qbsync.controller.salesreceiptline',
        // 'com:qbsync.controller.salesreceipt'                    => 'com://admin/qbsync.controller.salesreceipt',
        // 'com:nucleonplus.accounting.service.salesreceipt'       => 'com://admin/nucleonplus.accounting.service.salesreceipt',
        'com://site/nucleonplus.model.products'                 => 'com://admin/qbsync.model.items',
        'com:nucleonplus.model.packages'                        => 'com://admin/nucleonplus.model.packages',
        'com:nucleonplus.model.packageitems'                    => 'com://admin/nucleonplus.model.packageitems',
        'com://site/nucleonplus.model.members'                  => 'com://admin/nucleonplus.model.members',
        'com://site/nucleonplus.model.accounts'                 => 'com://admin/nucleonplus.model.accounts',
        'com://site/nucleonplus.model.payouts'                  => 'com://admin/nucleonplus.model.payouts',
        'com://site/nucleonplus.model.orders'                   => 'com://admin/nucleonplus.model.orders',
        'com://site/nucleonplus.model.packages'                 => 'com://admin/nucleonplus.model.packages',
        'com://site/nucleonplus.model.carts'                    => 'com://admin/nucleonplus.model.carts',
        'com://site/nucleonplus.model.entity.payout'            => 'com://admin/nucleonplus.model.entity.payout',
        'com://site/nucleonplus.database.table.orders'          => 'com://admin/nucleonplus.database.table.orders',
        'com://site/nucleonplus.database.table.payouts'         => 'com://admin/nucleonplus.database.table.payouts',
        'com://site/nucleonplus.template.helper.listbox'        => 'com://admin/nucleonplus.template.helper.listbox',
        'com://site/nucleonplus.controller.behavior.rewardable' => 'com://admin/nucleonplus.controller.behavior.rewardable',
        'com://site/nucleonplus.controller.behavior.referrable' => 'com://admin/nucleonplus.controller.behavior.referrable',
    ),
    'identifiers' => array(
        /*'com://site/nucleonplus.controller.order' => array(
            'behaviors' => array(
                'rewardable'
            ),
        ),*/
        'com://site/nucleonplus.database.table.orders' => array(
            'behaviors' => array(
                'com://site/nucleonplus.database.behavior.permissible'
            ),
        ),
    )
);