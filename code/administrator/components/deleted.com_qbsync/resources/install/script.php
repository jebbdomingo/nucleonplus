<?php
/**
 * QuickBooks Online Sync
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2016 - 2019 Nucleon + co. (http://www.nucleonplus.com)
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class com_qbsyncInstallerScript
{
    public function preflight($type, $installer)
    {
        $return = true;
        
        if (!class_exists('Koowa'))
        {
            $error = 'This component requires Nooku Framework';
            $installer->getParent()->abort($error);

            $return = false;
        }
        
        return $return;
    }
}