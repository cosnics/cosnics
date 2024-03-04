<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
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

    private function add(string $className, ?DataClassParameters $parameters, mixed $value): bool
    {
        if (!$this->exists($className, $parameters))
        {
            $this->set($className, $parameters->hash(), $value);
        }

        return true;
    }

    public function addForArrayCollection(
        string $cacheDataClassName, ArrayCollection $arrayCollection, DataClassParameters $parameters
    ): bool
    {
        return $this->add($cacheDataClassName, $parameters, $arrayCollection);
    }

    public function addForDataClass(
        string $cacheDataClassName, RetrieveParameters $parameters, ?DataClass $object = null
    ): bool
    {
        if ($object instanceof DataClass)
        {
            foreach ($object::getCacheablePropertyNames() as $cacheableProperty)
            {
                $value = $object->getDefaultProperty($cacheableProperty);

                if (isset($value))
                {
                    $cacheablePropertyParameters = new RetrieveParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable($cacheDataClassName, $cacheableProperty),
                            new StaticConditionVariable($value)
                        )
                    );
                    $this->set($cacheDataClassName, $cacheablePropertyParameters->hash(), $object);
                }
            }
        }

        $this->set($cacheDataClassName, $parameters->hash(), $object);

        return true;
    }

    public function addForDataClassCount(string $className, DataClassCountParameters $parameters, int $count): bool
    {
        return $this->add($className, $parameters, $count);
    }

    /**
     * @param int[] $counts
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

    public function addForRecord(string $className, array $record, RetrieveParameters $parameters): bool
    {
        $this->set($className, $parameters->hash(), $record);

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
            return null;
        }
    }

    public function reset(): void
    {
        $this->cache = [];
    }

    private function set(string $class, string $hash, mixed $value): void
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
