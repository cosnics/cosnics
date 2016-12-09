<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\ResultSet\DataClassResultSet;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassRepositoryCache now
 */
class DataClassResultSetCache extends DataClassCache
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet $result_set
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public static function add(DataClassResultSet $result_set, DataClassRetrievesParameters $parameters)
    {
        if (! $parameters instanceof DataClassRetrievesParameters)
        {
            throw new Exception('Illegal parameters passed to the DataClassResultSetCache');
        }
        
        if (! $result_set instanceof DataClassResultSet)
        {
            $type = is_object($result_set) ? get_class($result_set) : gettype($result_set);
            throw new Exception(
                'The DataClassResultSetCache cache only allows for caching of ResultSet objects. Currently trying to add: ' .
                     $type . '.');
        }
        
        if (! DataClassCache::get($result_set->getCacheClassName(), $parameters))
        {
            DataClassCache::set_cache($result_set->getCacheClassName(), $parameters->hash(), $result_set);
        }
        
        return true;
    }
}
