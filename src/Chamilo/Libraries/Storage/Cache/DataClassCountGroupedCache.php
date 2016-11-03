<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassRepositoryCache now
 */
class DataClassCountGroupedCache extends DataClassCache
{

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @param integer[] $counts
     * @throws Exception
     * @return boolean
     */
    public static function add($class, $parameters, $counts)
    {
        if (! $parameters instanceof DataClassCountGroupedParameters)
        {
            throw new Exception('Illegal parameters passed to the DataClassCountGroupedCache');
        }

        if (! is_array($counts))
        {
            $type = is_object($counts) ? get_class($counts) : gettype($counts);
            throw new Exception(
                'The DataClassCountGroupedCache cache only allows for caching of integer arrays. Currently trying to add: ' .
                     $type . '.');
        }

        if (! DataClassCache::exists($class, $parameters))
        {
            DataClassCache::set_cache($class, $parameters->hash(), $counts);
        }

        return true;
    }
}
