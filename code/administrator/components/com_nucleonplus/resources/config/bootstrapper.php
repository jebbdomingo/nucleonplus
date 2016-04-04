<?php

/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

return array(
    'identifiers' => array(
        'com://admin/nucleonplus.controller.account' => array(
            'behaviors' => array(
                'com:activities.controller.behavior.loggable',
            ),
        ),
        /*'com://admin/nucleonplus.controller.order' => array(
            'behaviors' => array(
                'com:nucleonplus.controller.behavior.rewardable',
            ),
        ),*/
    ),

    'aliases' => array()
);
