<?php

return array(
    'aliases' => array(
        'com:nucleonplus.model.packages'                        => 'com://admin/nucleonplus.model.packages',
        'com://site/nucleonplus.model.orders'                   => 'com://admin/nucleonplus.model.orders',
        'com://site/nucleonplus.database.table.orders'          => 'com://admin/nucleonplus.database.table.orders',
        'com://site/nucleonplus.template.helper.listbox'        => 'com://admin/nucleonplus.template.helper.listbox',
        'com://site/nucleonplus.controller.behavior.rewardable' => 'com://admin/nucleonplus.controller.behavior.rewardable',
        'com://site/nucleonplus.controller.behavior.referrable' => 'com://admin/nucleonplus.controller.behavior.referrable',
    ),
    'identifiers' => array(
        'com://site/nucleonplus.controller.order' => array(
            'behaviors' => array(
                'rewardable'
            ),
        ),
        'com://site/nucleonplus.database.table.orders' => array(
            'behaviors' => array(
                'com://site/nucleonplus.database.behavior.permissible'
            ),
        ),
    )
);