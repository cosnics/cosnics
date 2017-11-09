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
     * @param \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet $resultSet
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public static function add(DataClassResultSet $resultSet, DataClassRetrievesParameters $parameters)
    {
        if (! $parameters instanceof DataClassRetrievesParameters)
        {
            throw new Exception('Illegal parameters passed to the DataClassResultSetCache');
        }

        if (! $resultSet instanceof DataClassResultSet)
        {
            $type = is_object($resultSet) ? get_class($resultSet) : gettype($resultSet);
            throw new Exception(
                'The DataClassResultSetCache cache only allows for caching of ResultSet objects. Currently trying to add: ' .
                     $type . '.');
        }

        if (! DataClassCache::get($resultSet->getCacheClassName(), $parameters))
        {
            DataClassCache::set_cache($resultSet->getCacheClassName(), $parameters->hash(), $resultSet);
        }

        return true;
    }
}
