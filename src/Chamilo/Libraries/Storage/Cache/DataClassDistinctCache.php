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
     * @param string[] $propertyValues
     * @throws \Exception
     * @return boolean
     */
    public static function add($class, $parameters, $propertyValues)
    {
        if (! $parameters instanceof DataClassDistinctParameters)
        {
            throw new Exception('Illegal parameters passed to the DataClassDistinctCache');
        }

        if (! is_array($propertyValues))
        {
            $type = is_object($propertyValues) ? get_class($propertyValues) : gettype($propertyValues);
            throw new Exception(
                'The DataClassDistinctCache cache only allows for caching of string arrays. Currently trying to add: ' .
                     $type . '.');
        }

        if (! DataClassCache::exists($class, $parameters))
        {
            DataClassCache::set_cache($class, $parameters->hash(), $propertyValues);
        }

        return true;
    }
}
