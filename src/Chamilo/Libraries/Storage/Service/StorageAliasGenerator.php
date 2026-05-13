<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Interface\DoctrineEntityInterface;
use Symfony\Component\Uid\UuidV7;

/**
 *
 * @package Chamilo\Libraries\Storage\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StorageAliasGenerator
{
    public const int TYPE_CONSTRAINT = 2;
    public const int TYPE_TABLE = 1;

    /**
     * @var string[][]
     */
    private array $aliases = [];

    public function __construct()
    {
        foreach ($this->getTypes() as $type) {
            $this->aliases[$type] = [];
        }
    }

    /**
     * @return string[][]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param class-string<\Chamilo\Libraries\Storage\Architecture\Domain\DataClass> $class
     */
    public function getDataClassAlias(string $class): string
    {
        if (is_subclass_of($class, DoctrineEntityInterface::class)) {
            return $this->getTableAlias($class);
        }
        else {
            return $this->getTableAlias($class::getStorageUnitName());
        }
    }

    public function getTableAlias(string $tableName): string
    {
        if (!array_key_exists($tableName, $this->aliases[self::TYPE_TABLE])) {
            $uuid = new UuidV7();

            $this->aliases[self::TYPE_TABLE][$tableName] = $uuid->toBase58();
        }

        return $this->aliases[self::TYPE_TABLE][$tableName];



        // Return existing alias if already assigned
        if (isset($this->tableToAlias[$table])) {
            return $this->tableToAlias[$table];
        }

        // Base alias from hash
        $base = 't' . substr(md5($table), 0, 6);

        $alias = $base;
        $i = 1;

        // Resolve collisions
        while (isset($this->usedAliases[$alias])) {
            $alias = $base . $i;
            $i++;
        }

        // Register
        $this->tableToAlias[$table] = $alias;
        $this->usedAliases[$alias] = true;

        return $alias;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return [self::TYPE_TABLE, self::TYPE_CONSTRAINT];
    }
}
