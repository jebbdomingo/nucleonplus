<?php

return array(
    'aliases' => array(
        'com://site/nucleonplus.model.products'                 => 'com://admin/qbsync.model.items',
        'com:nucleonplus.model.packages'                        => 'com://site/rewardlabs.model.packages',
        'com:nucleonplus.model.packageitems'                    => 'com://site/rewardlabs.model.packageitems',
        'com://site/nucleonplus.model.members'                  => 'com://site/rewardlabs.model.members',
        'com://site/nucleonplus.database.accounts'                 => 'com://site/rewardlabs.database.accounts',
        'com://site/nucleonplus.model.accounts'                 => 'com://site/rewardlabs.model.accounts',
        'com://site/nucleonplus.model.payouts'                  => 'com://site/rewardlabs.model.payouts',
        'com://site/nucleonplus.model.orders'                   => 'com://site/rewardlabs.model.orders',
        'com://site/nucleonplus.model.packages'                 => 'com://site/rewardlabs.model.packages',
        'com://site/nucleonplus.controller.carts'                    => 'com://site/rewardlabs.controller.carts',
        'com://site/nucleonplus.model.carts'                    => 'com://site/rewardlabs.model.carts',
        'com://site/nucleonplus.model.entity.payout'            => 'com://site/rewardlabs.model.entity.payout',
        'com://site/nucleonplus.database.table.orders'          => 'com://site/rewardlabs.database.table.orders',
        'com://site/nucleonplus.database.table.payouts'         => 'com://site/rewardlabs.database.table.payouts',
        'com://site/nucleonplus.controller.behavior.rewardable' => 'com://site/rewardlabs.controller.behavior.rewardable',
        'com://site/nucleonplus.controller.behavior.referrable' => 'com://site/rewardlabs.controller.behavior.referrable',
    ),
    'identifiers' => array(
        'com://site/nucleonplus.database.table.orders' => array(
            'behaviors' => array(
                'com://site/nucleonplus.database.behavior.permissible'
            ),
        ),
    )
);