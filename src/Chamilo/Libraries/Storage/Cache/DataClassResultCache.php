<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use DataClassRepositoryCache now
 */
class DataClassResultCache extends DataClassCache
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     *
     * @return boolean
     * @throws Exception
     */
    public static function add(DataClass $object, DataClassRetrieveParameters $parameters = null)
    {
        if (!$object instanceof DataClass)
        {
            $type = is_object($object) ? get_class($object) : gettype($object);
            throw new Exception(
                'The DataClass cache only allows for caching of DataClass objects. Currently trying to add: ' . $type .
                '.'
            );
        }

        $class_name = self::getCacheClassName($object);

        foreach ($object->get_cacheable_property_names() as $cacheable_property)
        {
            $value = $object->get_default_property($cacheable_property);
            if (isset($value) && !is_null($value))
            {
                $cacheable_property_parameters = new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable($class_name, $cacheable_property),
                        new StaticConditionVariable($value)
                    )
                );
                DataClassCache::set_cache($class_name, $cacheable_property_parameters->hash(), $object);
            }
        }

        if ($parameters instanceof DataClassRetrieveParameters)
        {
            DataClassCache::set_cache($class_name, $parameters->hash(), $object);
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     *
     * @return boolean
     * @throws Exception
     */
    public static function delete(DataClass $object)
    {
        if (!$object instanceof DataClass)
        {
            throw new Exception('Not a DataClass');
        }

        $class_name = self::getCacheClassName($object);

        foreach ($object->get_cacheable_property_names() as $cacheable_property)
        {
            $cacheable_property_parameters = new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($class_name, $cacheable_property),
                    new StaticConditionVariable($object->get_default_property($cacheable_property))
                )
            );
            DataClassCache::set_cache($class_name, $cacheable_property_parameters->hash(), null);
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     *
     * @return string
     * @throws \ReflectionException
     */
    private static function getCacheClassName(DataClass $object)
    {
        $compositeDataClassName = CompositeDataClass::class_name();

        $isCompositeDataClass = $object instanceof $compositeDataClassName;
        $isExtensionClass = get_parent_class($object) !== $compositeDataClassName;

        if ($isCompositeDataClass && $isExtensionClass)
        {
            return $object::parent_class_name();
        }
        else
        {
            return $object::class_name();
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Exception\DataClassNoResultException $exception
     *
     * @return boolean
     */
    public static function no_result(DataClassNoResultException $exception)
    {
        DataClassCache::set_cache($exception->get_class_name(), $exception->get_parameters()->hash(), false);

        return true;
    }
}
