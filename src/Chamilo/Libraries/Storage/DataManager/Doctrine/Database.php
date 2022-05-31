<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Exception;

/**
 * This class provides basic functionality for database connections Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Replaced by the service-based DataClassDatabase and StorageUnitDatabase
 */
class Database
{
    use ClassContext;
    
    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string $property
     * @param string[] $attributes
     *
     * @return boolean
     * @throws \Exception
     */
    public function alter_storage_unit($type, $table_name, $property, $attributes = [])
    {
        return self::getStorageUnitDatabase()->alter($type, $table_name, $property, $attributes);
    }

    /**
     *
     * @param integer $type
     * @param string $table_name
     * @param string|null $name
     * @param string[] $columns
     *
     * @return boolean
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function alter_storage_unit_index($type, $table_name, $name = null, $columns = [])
    {
        return self::getStorageUnitDatabase()->alterIndex($type, $table_name, $name, $columns);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountParameters $parameters
     *
     * @return integer
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function count($class, $parameters)
    {
        return self::getDataClassDatabase()->count($class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassCountGroupedParameters $parameters
     *
     * @return integer[]|false
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function count_grouped($class, $parameters)
    {
        return self::getDataClassDatabase()->countGrouped($class, $parameters);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $object
     * @param boolean $auto_id
     *
     * @return boolean
     * @throws \Exception
     */
    public function create($object, $auto_id = true)
    {
        return self::getDataClassDatabase()->create($object, $auto_id);
    }

    /**
     *
     * @param string $class_name
     * @param string[] $record
     *
     * @return boolean
     * @throws \Exception
     */
    public function create_record($class_name, $record)
    {
        return self::getDataClassDatabase()->createRecord($class_name, $record);
    }

    /**
     * Creates a storage unit in the system
     *
     * @param string $name String the table name
     * @param string[][] $properties Array the table properties
     * @param string[][][] $indexes Array the table indexes
     *
     * @return true if the storage unit is succesfully created
     * @throws \Exception
     */
    public function create_storage_unit($name, $properties, $indexes)
    {
        return self::getStorageUnitDatabase()->create($name, $properties, $indexes);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function delete($class, $condition)
    {
        return self::getDataClassDatabase()->delete($class, $condition);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters $parameters
     *
     * @return string[]|boolean
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function distinct($class, DataClassDistinctParameters $parameters)
    {
        return self::getDataClassDatabase()->distinct($class, $parameters);
    }

    /**
     * Drop a given storage unit
     *
     * @param string $table_name
     *
     * @return boolean
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function drop_storage_unit($table_name)
    {
        return self::getStorageUnitDatabase()->drop($table_name);
    }

    /**
     * Escapes a column name in accordance with the database type.
     *
     * @param string $name The column name.
     * @param string|null $tableAlias The alias of the table the coloumn is in
     *
     * @return string The escaped column name.
     * @throws \Exception
     */
    public static function escape_column_name($name, $tableAlias = null)
    {
        return self::getDataClassDatabase()->escapeColumnName($name, $tableAlias);
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase
     * @throws \Exception
     */
    protected static function getDataClassDatabase()
    {
        return self:: getService('Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase');
    }

    /**
     * @param $serviceName
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase|\Chamilo\Libraries\Storage\DataManager\Doctrine\Database\DataClassDatabase
     * @throws \Exception
     */
    protected static function getService($serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase
     * @throws \Exception
     */
    protected static function getStorageUnitDatabase()
    {
        return self:: getService('Chamilo\Libraries\Storage\DataManager\Doctrine\Database\StorageUnitDatabase');
    }

    /**
     *
     * @param string $table_name
     *
     * @return string
     * @throws \Exception
     */
    public function get_alias($table_name)
    {
        return self::getDataClassDatabase()->getAlias($table_name);
    }

    /**
     *
     * @param string $table_name
     *
     * @return boolean
     * @throws \Exception
     */
    public function optimize_storage_unit($table_name)
    {
        return self::getStorageUnitDatabase()->optimize($table_name);
    }

    /**
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }

    /**
     *
     * @param string $value
     * @param string|null $type
     *
     * @return string
     * @throws \Exception
     */
    public static function quote($value, $type = null)
    {
        return self::getDataClassDatabase()->quote($value, $type);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters|null $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function record($class, $parameters = null)
    {
        return self::getDataClassDatabase()->record($class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $parameters
     *
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function records($class, RecordRetrievesParameters $parameters)
    {
        return self::getDataClassDatabase()->records($class, $parameters);
    }

    /**
     *
     * @param string $old_name
     * @param string $new_name
     *
     * @return boolean
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function rename_storage_unit($old_name, $new_name)
    {
        return self::getStorageUnitDatabase()->rename($old_name, $new_name);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters|null $parameters
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function retrieve($class, $parameters = null)
    {
        return self::getDataClassDatabase()->retrieve($class, $parameters);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $parameters
     *
     * @return \string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function retrieves($class, DataClassRetrievesParameters $parameters)
    {
        return self::getDataClassDatabase()->retrieves($class, $parameters);
    }

    /**
     *
     * @param string $name
     *
     * @return boolean
     * @throws \Exception
     */
    public function storage_unit_exists($name)
    {
        return self::getStorageUnitDatabase()->optimize($name);
    }

    /**
     *
     * @param mixed $function
     *
     * @return mixed
     * @throws \Throwable
     */
    public function transactional($function)
    {
        return self::getDataClassDatabase()->transactional($function);
    }

    /**
     *
     * @param Condition|null $condition
     *
     * @return string
     * @throws \Exception
     */
    public function translateCondition(Condition $condition = null)
    {
        return self::getDataClassDatabase()->translateCondition($condition);
    }

    /**
     *
     * @param string $table_name
     * @param boolean $optimize
     *
     * @return boolean
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function truncate_storage_unit($table_name, $optimize = true)
    {
        return self::getStorageUnitDatabase()->truncate($table_name, $optimize);
    }

    /**
     * @param string $dataClassStorageUnitName
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string[] $propertiesToUpdate
     *
     * @return boolean
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function update($dataClassStorageUnitName, Condition $condition, $propertiesToUpdate)
    {
        return self::getDataClassDatabase()->update($dataClassStorageUnitName, $condition, $propertiesToUpdate);
    }

    /**
     *
     * @param string $class
     * @param \Chamilo\Libraries\Storage\Query\RetrieveProperties $properties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return boolean
     * @throws Exception
     */
    public function updates($class, $properties, $condition)
    {
        return self::getDataClassDatabase()->updates($class, $properties, $condition);
    }
}
