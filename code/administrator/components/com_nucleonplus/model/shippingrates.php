<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusModelShippingrates extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('packaging', 'string')
            ->insert('destination', 'string')
        ;
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if ($state->packaging) {
            $query->where('tbl.packaging IN :packaging')->bind(['packaging' => (array) $state->packaging]);
        }

        if ($state->destination) {
            $query->where('tbl.destination IN :destination')->bind(['destination' => (array) $state->destination]);
        }
    }

    /**
     * Get the rate based on destination and weight of the package
     *
     * @param string  $destination
     * @param integer $weight
     *
     * @return bool|float
     */
    public function getRate($destination, $weight)
    {
        $state        = $this->getState();
        $result       = false;
        $maxWeight    = 3000; // 3kg
        $additionalKg = 1000;

        if ($weight > $maxWeight)
        {
            // Get rate for additional kg
            $identifier = $destination == 'metro_manila' ? 'metro_manila_kg' : 'provincial_kg';
            $ratePerKg  = $this->_getRate($identifier, $additionalKg);

            // Compute additional rate
            $numExcessKg    = round(($weight - $maxWeight), -3) / $additionalKg;
            $additionalRate = $ratePerKg * $numExcessKg;

            // Compute total
            $maxRate = $this->_getRate($destination, $maxWeight);
            $result  = $maxRate + $additionalRate;
        }
        else $result = $this->_getRate($destination, $weight);

        return $result;
    }

    protected function _getRate($destination, $weight)
    {
        $table = $this->getObject('com://admin/nucleonplus.database.table.shippingrates');
        $query = $this->getObject('database.query.select')
            ->table('nucleonplus_shippingrates AS tbl')
            ->columns('tbl.nucleonplus_shippingrate_id, tbl.rate AS rate')
            ->where('tbl.destination = :destination')->bind(['destination' => $destination])
            ->where(':weight <= tbl.max_weight')->bind(['weight' => $weight])
            ->order('tbl.max_weight')
            ->limit(1)
        ;

        $result = $table->select($query);

        return (float) $result->rate;
    }
}
