<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\RetrieveParameters;
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
    public const TYPE_COUNT = 1;
    public const TYPE_COUNT_GROUPED = 2;
    public const TYPE_DISTINCT = 3;
    public const TYPE_RECORD = 4;
    public const TYPE_RECORDS = 5;
    public const TYPE_RETRIEVE = 6;
    public const TYPE_RETRIEVES = 7;

    /**
     * The cache
     *
     * @var array[][][]
     */
    private array $cache;

    public function __construct()
    {
        $this->cache = [];
    }

    private function add(string $className, int $type, ?DataClassParameters $parameters, mixed $value): mixed
    {
        if (!$this->existsForType($type, $className, $parameters))
        {
            $this->setForType($type, $className, $parameters->hash(), $value);
        }

        return $this->getForType($type, $className, $parameters);
    }

    public function addForCount(
        string $className, DataClassCountParameters $parameters, int $count
    ): int
    {
        return $this->add($className, self::TYPE_COUNT, $parameters, $count);
    }

    /**
     * @param int[] $counts
     *
     * @return int[]
     */
    public function addForCountGrouped(
        string $className, DataClassCountGroupedParameters $parameters, array $counts
    ): array
    {
        return $this->add($className, self::TYPE_COUNT_GROUPED, $parameters, $counts);
    }

    public function addForDistinct(
        string $className, DataClassDistinctParameters $parameters, array $propertyValues
    ): array
    {
        return $this->add($className, self::TYPE_DISTINCT, $parameters, $propertyValues);
    }

    public function addForRecord(string $className, RetrieveParameters $parameters, array $record): array
    {
        return $this->add($className, self::TYPE_RECORD, $parameters, $record);
    }

    public function addForRecords(
        string $cacheDataClassName, DataClassParameters $parameters, ArrayCollection $arrayCollection
    ): ArrayCollection
    {
        return $this->add($cacheDataClassName, self::TYPE_RECORDS, $parameters, $arrayCollection);
    }

    public function addForRetrieve(
        string $cacheDataClassName, RetrieveParameters $parameters, ?DataClass $object = null
    ): ?DataClass
    {
        return $this->add($cacheDataClassName, self::TYPE_RETRIEVE, $parameters, $object);
    }

    public function addForRetrieves(
        string $cacheDataClassName, DataClassParameters $parameters, ArrayCollection $arrayCollection
    ): ArrayCollection
    {
        return $this->add($cacheDataClassName, self::TYPE_RETRIEVES, $parameters, $arrayCollection);
    }

    public function existsForType(int $type, string $class, DataClassParameters $parameters): bool
    {
        $hash = $parameters->hash();

        if (isset($this->cache[$class][$type][$hash]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getForType(int $type, string $class, DataClassParameters $parameters)
    {
        if ($this->existsForType($type, $class, $parameters))
        {
            return $this->cache[$class][$type][$parameters->hash()];
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

    private function setForType(int $type, string $class, string $hash, mixed $value): void
    {
        $this->cache[$class][$type][$hash] = $value;
    }

    public function truncateClass(string $class): bool
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
    public function truncateClasses(array $classes = []): bool
    {
        foreach ($classes as $class)
        {
            if (!$this->truncateClass($class))
            {
                return false;
            }
        }

        return true;
    }
}
