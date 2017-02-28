<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRepositoryCache
{

    /**
     * The cache
     *
     * @var mixed[][]
     */
    private $cache;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cache = array();
    }

    /**
     * Get a DataClass object from the cache
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @return boolean
     */
    public function get($class, DataClassParameters $parameters)
    {
        if ($this->exists($class, $parameters))
        {
            return $this->cache[$class][$parameters->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Returns whether a DataClass object exists in the cache
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters $parameters
     * @return boolean
     */
    public function exists($class, DataClassParameters $parameters)
    {
        $hash = $parameters->hash();

        if (isset($this->cache[$class][$hash]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Clear the cache for a specific DataClass type
     *
     * @param string $class
     * @return boolean
     */
    public function truncate($class)
    {
        if (isset($this->cache[$class]))
        {
            unset($this->cache[$class]);
        }

        return true;
    }

    /**
     * Clear the cache for a set of specific DataClass types
     *
     * @param string[] $classes
     * @return boolean
     */
    public function truncates($classes = array())
    {
        foreach ($classes as $class)
        {
            if (! $this->truncate($class))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Set the cache value for a specific DataClass object type, hash
     *
     * @param string $class
     * @param string $hash
     * @param mixed $value
     */
    private function set($class, $hash, $value)
    {
        $this->cache[$class][$hash] = $value;
    }

    /**
     *
     * @param string $class
     * @param DataClassParameters $parameters
     * @param mixed $value
     * @return boolean
     */
    private function add($className, DataClassParameters $parameters = null, $value)
    {
        if (! $this->exists($className, $parameters))
        {
            $this->set($className, $parameters->hash(), $value);
        }

        return true;
    }

    public function reset()
    {
        $this->cache = array();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public function addForDataClass(DataClass $object, DataClassRetrieveParameters $parameters = null)
    {
        if (! $parameters instanceof DataClassRetrieveParameters && $parameters != null)
        {
            throw new \Exception('Illegal parameters passed to the DataClassServiceCache');
        }

        if (! $object instanceof DataClass)
        {
            $type = is_object($object) ? get_class($object) : gettype($object);
            throw new \Exception(
                'The DataClassServiceCache only allows for caching of DataClass objects. Currently trying to add: ' .
                     $type . '.');
        }

        $className = $this->getDataClassCacheClassName($object);

        foreach ($object->get_cacheable_property_names() as $cacheableProperty)
        {
            $value = $object->get_default_property($cacheableProperty);
            if (isset($value) && ! is_null($value))
            {
                $cacheablePropertyParameters = new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable($className, $cacheableProperty),
                        new StaticConditionVariable($value)));
                $this->set($className, $cacheablePropertyParameters->hash(), $object);
            }
        }

        if ($parameters instanceof DataClassRetrieveParameters)
        {
            $this->set($className, $parameters->hash(), $object);
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @return string
     */
    private function getDataClassCacheClassName(DataClass $object)
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
     * @return boolean
     */
    public function addForNoResult(DataClassNoResultException $exception)
    {
        $this->set($exception->get_class_name(), $exception->get_parameters()->hash(), false);
        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @throws Exception
     * @return boolean
     */
    public function deleteForDataClass(DataClass $object)
    {
        if (! $object instanceof DataClass)
        {
            throw new \Exception('Not a DataClass');
        }

        $className = $this->getDataClassCacheClassName($object);

        foreach ($object->get_cacheable_property_names() as $cacheableProperty)
        {
            $cacheablePropertyParameters = new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($className, $cacheableProperty),
                    new StaticConditionVariable($object->get_default_property($cacheableProperty))));
            $this->set($className, $cacheablePropertyParameters->hash(), null);
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Iterator\DataClassIterator $dataClassIterator
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public function addForDataClassIterator(DataClassIterator $dataClassIterator,
        DataClassRetrievesParameters $parameters)
    {
        if (! $parameters instanceof DataClassRetrievesParameters)
        {
            throw new \Exception('Illegal parameters passed to the DataClassResultSetCache');
        }

        if (! $dataClassIterator instanceof DataClassIterator)
        {
            $type = is_object($dataClassIterator) ? get_class($dataClassIterator) : gettype($dataClassIterator);
            throw new \Exception(
                'The DataClassResultSetCache cache only allows for caching of ResultSet objects. Currently trying to add: ' .
                     $type . '.');
        }

        return $this->add($dataClassIterator->getCacheClassName(), $parameters, $dataClassIterator);
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     * @param integer $count
     * @throws Exception
     * @return boolean
     */
    public function addForDataClassCount($className, $parameters, $count)
    {
        if (! $parameters instanceof DataClassCountParameters)
        {
            throw new \Exception('Illegal parameters passed to the DataClassCountCache');
        }

        if (! is_integer($count))
        {
            $type = is_object($count) ? get_class($count) : gettype($count);
            throw new \Exception(
                'The DataClassCountCache cache only allows for caching of integers. Currently trying to add: ' . $type .
                     '.');
        }

        return $this->add($className, $parameters, $count);
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     * @param string[] $property_values
     * @throws Exception
     * @return boolean
     */
    public function addForDataClassDistinct($className, $parameters, $propertyValues)
    {
        if (! $parameters instanceof DataClassDistinctParameters)
        {
            throw new \Exception('Illegal parameters passed to the DataClassDistinctCache');
        }

        if (! is_array($propertyValues))
        {
            $type = is_object($propertyValues) ? get_class($propertyValues) : gettype($propertyValues);
            throw new \Exception(
                'The DataClassDistinctCache cache only allows for caching of string arrays. Currently trying to add: ' .
                     $type . '.');
        }

        return $this->add($className, $parameters, $propertyValues);
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @param integer[] $counts
     * @throws Exception
     * @return boolean
     */
    public function addForDataClassCountGrouped($className, $parameters, $counts)
    {
        if (! $parameters instanceof DataClassCountGroupedParameters)
        {
            throw new \Exception('Illegal parameters passed to the DataClassCountGroupedCache');
        }

        if (! is_array($counts))
        {
            $type = is_object($counts) ? get_class($counts) : gettype($counts);
            throw new \Exception(
                'The DataClassCountGroupedCache cache only allows for caching of integer arrays. Currently trying to add: ' .
                     $type . '.');
        }

        return $this->add($className, $parameters, $counts);
    }

    /**
     *
     * @param string[] $record
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public function addForRecord($className, $record, RecordRetrieveParameters $parameters = null)
    {
        if (! is_array($record))
        {
            throw new \Exception(
                'The RecordResultCache only allows for caching of records. Currently trying to add: ' . gettype($record) .
                     '.');
        }

        if ($parameters instanceof RecordRetrieveParameters)
        {
            $this->set($className, $parameters->hash(), $record);
        }

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Iterator\RecordIterator $recordIterator
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     * @throws \Exception
     * @return boolean
     */
    public function addForRecordIterator($className, RecordIterator $recordIterator,
        RecordRetrievesParameters $parameters)
    {
        if (! $parameters instanceof RecordRetrievesParameters)
        {
            throw new \Exception('Illegal parameters passed to the RecordResultSetCache');
        }

        if (! $recordIterator instanceof RecordIterator)
        {
            $type = is_object($recordIterator) ? get_class($recordIterator) : gettype($recordIterator);
            throw new \Exception(
                'The RecordResultSetCache cache only allows for caching of ResultSet objects. Currently trying to add: ' .
                     $type . '.');
        }

        return $this->add($className, $parameters, $recordIterator);
    }
}
