<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassCountCache extends DataClassCache
{

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @param integer $count
     * @throws Exception
     * @return boolean
     */
    public static function add($class, $parameters, $count)
    {
        if (! $parameters instanceof DataClassCountParameters)
        {
            throw new Exception('Illegal parameters passed to the DataClassCountCache');
        }

        if (! is_integer($count))
        {
            $type = is_object($count) ? get_class($count) : gettype($count);
            throw new Exception(
                'The DataClassCountCache cache only allows for caching of integers. Currently trying to add: ' . $type .
                     '.');
        }

        if (! DataClassCache::exists($class, $parameters))
        {
            DataClassCache::set_cache($class, $parameters->hash(), $count);
        }

        return true;
    }
}
