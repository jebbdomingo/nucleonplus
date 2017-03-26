<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('_JEXEC') or die;

if (class_exists('Koowa'))
{
    echo KObjectManager::getInstance()->getObject('mod://site/nucleonplus_login.html')
        ->module($module)
        ->attribs($attribs)
        ->render();
}
