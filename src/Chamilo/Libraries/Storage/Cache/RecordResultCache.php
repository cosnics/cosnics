<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassRepositoryCache now
 */
class RecordResultCache extends RecordCache
{

    /**
     *
     * @param string[] $record
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public static function add($className, $record, RecordRetrieveParameters $parameters = null)
    {
        if (! is_array($record))
        {
            throw new Exception(
                'The RecordResultCache only allows for caching of records. Currently trying to add: ' . gettype($record) .
                     '.');
        }

        if ($parameters instanceof RecordRetrieveParameters)
        {
            self::set_cache($className, $parameters->hash(), $record);
        }

        return true;
    }

    /**
     * Process a DataClassNoResultException for the DataClassCache, preventing continued access to the storage layer for
     * empty results
     *
     * @param \Chamilo\Libraries\Storage\Exception\DataClassNoResultException $exception
     * @return boolean
     */
    public static function no_result(DataClassNoResultException $exception)
    {
        self::set_cache($exception->get_class_name(), $exception->get_parameters()->hash(), false);
        return true;
    }
}
