<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewOrderHtml extends ComNucleonplusViewHtml
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $query = $this->getUrl()->getQuery(true);

        if (isset($query['package_id']) && $query['package_id']) {
            $this->_data['package_id'] = $query['package_id'];
        }
        else $this->_data['package_id'] = null;
    }
}