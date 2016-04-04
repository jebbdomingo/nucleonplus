<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
class ComNucleonplusViewOrderHtml extends KViewHtml
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

        if (isset($query['account_id']) && $query['account_id']) {
            $this->_data['account_id'] = $query['account_id'];
        }
        else $this->_data['account_id'] = null;
    }
}
