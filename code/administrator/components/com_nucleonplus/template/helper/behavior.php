<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusTemplateHelperBehavior extends ComKoowaTemplateHelperBehavior
{
    public function calendar($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'format' => '%Y-%m-%d'
        ));

        return parent::calendar($config);
    }
}
