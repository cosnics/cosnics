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
     * @var array[][]
     */
    private array $cache;

    public function __construct()
    {
        $this->cache = [];
    }

    /**
     * @param mixed $value
     */
    private function add(string $className, ?DataClassParameters $parameters, $value): bool
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
     * @param integer[] $counts
     *
     * @throws \Exception
     */
    public function addForDataClassCountGrouped(
        string $className, DataClassCountGroupedParameters $parameters, array $counts
    ): bool
    {
        return $this->add($className, $parameters, $counts);
    }

    public function addForDataClassDistinct(
        string $className, DataClassDistinctParameters $parameters, array $propertyValues
    ): bool
    {
        return $this->add($className, $parameters, $propertyValues);
    }

    public function addForNoResult(DataClassNoResultException $exception): bool
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
     * @throws \ReflectionException
     */
    public function deleteForDataClass(DataClass $object): bool
    {
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

    public function exists(string $class, DataClassParameters $parameters): bool
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

    public function get(string $class, DataClassParameters $parameters)
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

    protected function getCacheClassName(string $dataClassName): string
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
     * @throws \ReflectionException
     */
    private function getDataClassCacheClassName(DataClass $object): string
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
     * @param mixed $value
     */
    private function set(string $class, string $hash, $value)
    {
        $this->cache[$class][$hash] = $value;
    }

    public function truncate(string $class): bool
    {
        if (isset($this->cache[$class]))
        {
            unset($this->cache[$class]);
        }

        return true;
    }

    /**
     * @param string[] $classes
     */
    public function truncates(array $classes = []): bool
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
