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
    const TYPE_CONSTRAINT = 2;
    const TYPE_TABLE = 1;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    private $classnameUtilities;

    /**
     *
     * @var string[][]
     */
    private $aliases = array();

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function __construct(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;

        foreach ($this->getTypes() as $type)
        {
            $this->aliases[$type] = array();
        }
    }

    /**
     *
     * @return string[][]
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     *
     * @return string[][]
     * @deprecated Use getAliases() now
     */
    public function get_aliases()

    {
        return $this->getAliases();
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ClassnameUtilities
     */
    public function getClassnameUtilities()
    {
        return $this->classnameUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ClassnameUtilities $classnameUtilities
     */
    public function setClassnameUtilities(ClassnameUtilities $classnameUtilities)
    {
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     *
     * @param string $tableName
     * @param string $column
     *
     * @return string
     */
    public function getConstraintName($tableName, $column)
    {
        $possibleName = '';
        $parts = explode('_', $tableName);

        foreach ($parts as $part)
        {
            $possibleName .= $part[0];
        }

        $possibleName = $possibleName . '_' . $column;

        if (!array_key_exists($possibleName, $this->aliases[self::TYPE_CONSTRAINT]))
        {
            $this->aliases[self::TYPE_CONSTRAINT][$possibleName] = serialize(array($tableName, $column));

            return $possibleName;
        }
        else
        {
            $originalName = $possibleName;
            $index = 'a';

            while (array_key_exists($possibleName, $this->aliases[self::TYPE_CONSTRAINT]))
            {
                $possibleName = $originalName . '_' . $index;
                $index ++;
            }

            $this->aliases[self::TYPE_CONSTRAINT][$possibleName] = serialize(array($tableName, $column));

            return $possibleName;
        }
    }

    /**
     *
     * @param string $class
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getDataClassAlias($class)
    {
        /**
         * @var \Chamilo\Libraries\Storage\DataClass\DataClass $class
         */
        return $this->getTableAlias($class::get_table_name());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self(ClassnameUtilities::getInstance());
        }

        return self::$instance;
    }

    /**
     *
     * @param string $tableName
     *
     * @return string
     */
    public function getTableAlias($tableName)
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
     *
     * @return string[]
     */
    public function getTypes()
    {
        return array(self::TYPE_TABLE, self::TYPE_CONSTRAINT);
    }

    /**
     *
     * @param string $table_name
     * @param string $column
     *
     * @return string
     * @deprecated Use getConstraintName() now
     */
    public function get_constraint_name($table_name, $column)
    {
        return $this->getConstraintName($table_name, $column);
    }

    /**
     *
     * @param string $class
     *
     * @return string
     * @throws \ReflectionException
     * @deprecated Use getDataClassAlias() now
     */
    public function get_data_class_alias($class)
    {
        return $this->getDataClassAlias($class);
    }

    /**
     *
     * @param string $tableName
     *
     * @return string
     * @deprecated Use getTableAlias() now
     */
    public function get_table_alias($tableName)
    {
        return $this->getTableAlias($tableName);
    }

    /**
     *
     * @return string[]
     * @deprecated Use getTypes() now
     */
    public function get_types()

    {
        return $this->getTypes();
    }
}
