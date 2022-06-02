<?php
namespace Chamilo\Libraries\Storage\DataManager;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StorageAliasGenerator
{
    public const TYPE_CONSTRAINT = 2;
    public const TYPE_TABLE = 1;

    private static StorageAliasGenerator $instance;

    /**
     *
     * @var string[][]
     */
    private array $aliases = [];

    private ClassnameUtilities $classnameUtilities;

    public function __construct(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;

        foreach ($this->getTypes() as $type)
        {
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
     * @return string[][]
     * @deprecated Use getAliases() now
     */
    public function get_aliases(): array
    {
        return $this->getAliases();
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities): StorageAliasGenerator
    {
        $this->classnameUtilities = $classnameUtilities;

        return $this;
    }

    public function getConstraintName(string $tableName, string $column): string
    {
        $possibleName = '';
        $parts = explode('_', $tableName);

        foreach ($parts as $part)
        {
            $possibleName .= $part[0];
        }

        $possibleName = $possibleName . '_' . $column;

        if (array_key_exists($possibleName, $this->aliases[self::TYPE_CONSTRAINT]))
        {
            $originalName = $possibleName;
            $index = 'a';

            while (array_key_exists($possibleName, $this->aliases[self::TYPE_CONSTRAINT]))
            {
                $possibleName = $originalName . '_' . $index;
                $index ++;
            }
        }

        $this->aliases[self::TYPE_CONSTRAINT][$possibleName] = serialize([$tableName, $column]);

        return $possibleName;
    }

    public function getDataClassAlias(string $class): string
    {
        return $this->getTableAlias($class::getStorageUnitName());
    }

    public static function getInstance(): StorageAliasGenerator
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self(ClassnameUtilities::getInstance());
        }

        return self::$instance;
    }

    public function getTableAlias(string $tableName): string
    {
        if (array_key_exists($tableName, $this->aliases[self::TYPE_TABLE]))
        {
            return $this->aliases[self::TYPE_TABLE][$tableName];
        }
        else
        {
            $possibleName = 'alias_';
            $parts = explode('_', $tableName);

            foreach ($parts as $part)
            {
                $possibleName .= $part[0];
            }

            if (in_array($possibleName, $this->aliases[self::TYPE_TABLE]))
            {
                $originalName = $possibleName;
                $index = 'a';

                while (in_array($possibleName, $this->aliases[self::TYPE_TABLE]))
                {
                    $possibleName = $originalName . '_' . $index;
                    $index ++;
                }
            }

            $this->aliases[self::TYPE_TABLE][$tableName] = $possibleName;

            return $possibleName;
        }
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return [self::TYPE_TABLE, self::TYPE_CONSTRAINT];
    }

    /**
     * @deprecated Use getConstraintName() now
     */
    public function get_constraint_name(string $table_name, string $column): string
    {
        return $this->getConstraintName($table_name, $column);
    }

    /**
     * @deprecated Use getDataClassAlias() now
     */
    public function get_data_class_alias(string $class): string
    {
        return $this->getDataClassAlias($class);
    }

    /**
     * @deprecated Use getTableAlias() now
     */
    public function get_table_alias(string $tableName): string
    {
        return $this->getTableAlias($tableName);
    }

    /**
     *
     * @return string[]
     * @deprecated Use getTypes() now
     */
    public function get_types(): array

    {
        return $this->getTypes();
    }
}
