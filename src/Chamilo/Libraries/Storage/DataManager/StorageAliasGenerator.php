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

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities): StorageAliasGenerator
    {
        $this->classnameUtilities = $classnameUtilities;

        return $this;
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
     *
     * @return string[]
     * @deprecated Use getTypes() now
     */
    public function get_types(): array

    {
        return $this->getTypes();
    }
}
