<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassRepositoryCache now
 */
class DataClassDistinctCache extends DataClassCache
{

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     * @param string[] $property_values
     * @throws Exception
     * @return boolean
     */
    public static function add($class, $parameters, $property_values)
    {
        if (! $parameters instanceof DataClassDistinctParameters)
        {
            throw new Exception('Illegal parameters passed to the DataClassDistinctCache');
        }

        if (! is_array($property_values))
        {
            $type = is_object($property_values) ? get_class($property_values) : gettype($property_values);
            throw new Exception(
                'The DataClassDistinctCache cache only allows for caching of string arrays. Currently trying to add: ' .
                     $type . '.');
        }

        if (! DataClassCache::exists($class, $parameters))
        {
            DataClassCache::set_cache($class, $parameters->hash(), $property_values);
        }

        return true;
    }
}
