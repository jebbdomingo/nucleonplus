<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusAccountingServiceData extends KObject
{
    /**
     * QBO Data
     *
     * @var array
     */
    protected $_data;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        $env  = getenv('APP_ENV');
        $data = parse_ini_file('data.ini', true);

        $this->_data = $data[$env];
    }

    /**
     * Getter
     *
     * @param string $name Name of the property
     *
     * @return mixed
     */
    public function __get($name)
    {
        $name = strtoupper($name);

        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $trace = debug_backtrace();
        
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);

        return null;
    }
}