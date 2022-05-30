<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

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
        $this->cache = [];
    }

    /**
     *
     * @param string $className
     * @param ?\Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     * @param mixed $value
     *
     * @return boolean
     */
    private function add($className, DataClassParameters $parameters = null, $value)
    {
        if (!$this->exists($className, $parameters))
        {
            $this->set($className, $parameters->hash(), $value);
        }

        return true;
    }

    public function addForArrayCollection(
        string $dataClassName, ArrayCollection $arrayCollection, DataClassParameters $parameters
    ): bool
    {
        return $this->add($this->getCacheClassName($dataClassName), $parameters, $arrayCollection);
    }

    /**
     * @throws \ReflectionException
     */
    public function addForDataClass(DataClass $object, DataClassRetrieveParameters $parameters): bool
    {
        $className = $this->getDataClassCacheClassName($object);

        foreach ($object->getCacheablePropertyNames() as $cacheableProperty)
        {
            $value = $object->getDefaultProperty($cacheableProperty);
            if (isset($value))
            {
                $cacheablePropertyParameters = new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable($className, $cacheableProperty),
                        new StaticConditionVariable($value)
                    )
                );
                $this->set($className, $cacheablePropertyParameters->hash(), $object);
            }
        }

        $this->set($className, $parameters->hash(), $object);

        return true;
    }

    public function addForDataClassCount(string $className, DataClassCountParameters $parameters, int $count): bool
    {
        return $this->add($className, $parameters, $count);
    }

    /**
     *
     * @param string $className
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     * @param integer[] $counts
     *
     * @return boolean
     * @throws \Exception
     */
    public function addForDataClassCountGrouped($className, $parameters, $counts)
    {
        if (!$parameters instanceof DataClassCountGroupedParameters)
        {
            throw new Exception('Illegal parameters passed to the DataClassRepositoryCache');
        }

        if (!is_array($counts))
        {
            $type = is_object($counts) ? get_class($counts) : gettype($counts);
            throw new Exception(
                'DataClassRepositoryCache::addForDataClassCountGrouped only allows for caching of integer arrays. Currently trying to add: ' .
                $type . '.'
            );
        }

        return $this->add($className, $parameters, $counts);
    }

    public function addForDataClassDistinct(
        string $className, DataClassDistinctParameters $parameters, array $propertyValues
    ): bool
    {
        return $this->add($className, $parameters, $propertyValues);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Exception\DataClassNoResultException $exception
     *
     * @return boolean
     */
    public function addForNoResult(DataClassNoResultException $exception)
    {
        $this->set($exception->get_class_name(), $exception->get_parameters()->hash(), false);

        return true;
    }

    public function addForRecord(string $className, array $record, RecordRetrieveParameters $parameters): bool
    {
        $this->set($className, $parameters->hash(), $record);

        return true;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     *
     * @return boolean
     * @throws \Exception
     */
    public function deleteForDataClass(DataClass $object)
    {
        if (!$object instanceof DataClass)
        {
            throw new Exception('Not a DataClass');
        }

        $className = $this->getDataClassCacheClassName($object);

        foreach ($object->getCacheablePropertyNames() as $cacheableProperty)
        {
            $cacheablePropertyParameters = new DataClassRetrieveParameters(
                new EqualityCondition(
                    new PropertyConditionVariable($className, $cacheableProperty),
                    new StaticConditionVariable($object->getDefaultProperty($cacheableProperty))
                )
            );
            $this->set($className, $cacheablePropertyParameters->hash(), null);
        }

        return true;
    }

    /**
     * Returns whether a DataClass object exists in the cache
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
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
     * Get a DataClass object from the cache
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $parameters
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|boolean|\Doctrine\Common\Collections\ArrayCollection|array
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

    protected function getCacheClassName($dataClassName): string
    {
        $isCompositeDataClass = is_subclass_of($dataClassName, CompositeDataClass::class);
        $isExtensionClass = get_parent_class($dataClassName) !== CompositeDataClass::class;

        if ($isCompositeDataClass && $isExtensionClass)
        {
            return $dataClassName::parentClassName();
        }
        else
        {
            return $dataClassName;
        }
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     *
     * @return string
     * @throws \ReflectionException
     */
    private function getDataClassCacheClassName(DataClass $object)
    {
        $compositeDataClassName = CompositeDataClass::class;

        $isCompositeDataClass = $object instanceof $compositeDataClassName;
        $isExtensionClass = get_parent_class($object) !== $compositeDataClassName;

        if ($isCompositeDataClass && $isExtensionClass)
        {
            return $object::parentClassName();
        }
        else
        {
            return $object::class_name();
        }
    }

    public function reset()
    {
        $this->cache = [];
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
     * Clear the cache for a specific DataClass type
     *
     * @param string $class
     *
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
     *
     * @return boolean
     */
    public function truncates($classes = [])
    {
        foreach ($classes as $class)
        {
            if (!$this->truncate($class))
            {
                return false;
            }
        }

        return true;
    }
}
