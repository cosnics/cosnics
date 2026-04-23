<?php
namespace Chamilo\Libraries\Storage\Architecture\Domain;

use Chamilo\Libraries\Storage\Architecture\Domain\Enum\CacheTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Libraries\Storage\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataClassRepositoryCache
{
    /**
     * @var array[][][]
     */
    private array $cache;

    public function __construct()
    {
        $this->cache = [];
    }

    private function add(string $className, CacheTypeEnum $type, ?StorageParameters $parameters, callable $value): mixed
    {
        if (!$this->existsForType($type, $className, $parameters)) {
            $this->setForType($type, $className, $parameters->hash(), $value());
        }

        return $this->getForType($type, $className, $parameters);
    }

    public function addForCount(
        string $className, StorageParameters $parameters, callable $value
    ): int
    {
        return $this->add($className, CacheTypeEnum::COUNT, $parameters, $value);
    }

    /**
     * @return int[]
     */
    public function addForCountGrouped(
        string $className, StorageParameters $parameters, callable $value
    ): array
    {
        return $this->add($className, CacheTypeEnum::COUNT_GROUPED, $parameters, $value);
    }

    public function addForDistinct(
        string $className, StorageParameters $parameters, callable $value
    ): array
    {
        return $this->add($className, CacheTypeEnum::DISTINCT, $parameters, $value);
    }

    public function addForRecord(string $className, StorageParameters $parameters, callable $value): array
    {
        return $this->add($className, CacheTypeEnum::RECORD, $parameters, $value);
    }

    public function addForRecords(
        string $cacheDataClassName, StorageParameters $parameters, callable $value
    ): ArrayCollection
    {
        return $this->add($cacheDataClassName, CacheTypeEnum::RECORDS, $parameters, $value);
    }

    public function addForRetrieve(
        string $cacheDataClassName, StorageParameters $parameters, callable $value
    ): ?DataClass
    {
        return $this->add($cacheDataClassName, CacheTypeEnum::RETRIEVE, $parameters, $value);
    }

    public function addForRetrieves(
        string $cacheDataClassName, StorageParameters $parameters, callable $value
    ): ArrayCollection
    {
        return $this->add($cacheDataClassName, CacheTypeEnum::RETRIEVES, $parameters, $value);
    }

    public function existsForType(CacheTypeEnum $type, string $class, StorageParameters $parameters): bool
    {
        $hash = $parameters->hash();

        if (isset($this->cache[$class][$type->value][$hash])) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getForType(CacheTypeEnum $type, string $class, StorageParameters $parameters)
    {
        if ($this->existsForType($type, $class, $parameters)) {
            return $this->cache[$class][$type->value][$parameters->hash()];
        }
        else {
            return null;
        }
    }

    public function reset(): void
    {
        $this->cache = [];
    }

    private function setForType(CacheTypeEnum $type, string $class, string $hash, mixed $value): void
    {
        $this->cache[$class][$type->value][$hash] = $value;
    }

    public function truncateClass(string $class): bool
    {
        if (isset($this->cache[$class])) {
            unset($this->cache[$class]);
        }

        return true;
    }
}
