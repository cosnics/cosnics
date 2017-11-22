<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\ResultSet\RecordResultSet;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassRepositoryCache now
 */
class RecordResultSetCache extends RecordCache
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\ResultSet\RecordResultSet $resultSet
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public static function add($className, RecordResultSet $resultSet, RecordRetrievesParameters $parameters)
    {
        if (! $parameters instanceof RecordRetrievesParameters)
        {
            throw new Exception('Illegal parameters passed to the RecordResultSetCache');
        }

        if (! $resultSet instanceof RecordResultSet)
        {
            $type = is_object($resultSet) ? get_class($resultSet) : gettype($resultSet);
            throw new Exception(
                'The RecordResultSetCache cache only allows for caching of ResultSet objects. Currently trying to add: ' .
                     $type . '.');
        }

        if (! self::get($className, $parameters))
        {
            self::set_cache($className, $parameters->hash(), $resultSet);
        }

        return true;
    }
}
