<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Database;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;

/**
 * This class provides basic functionality for storage unit manipulations via Doctrine
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StorageUnitDatabase implements StorageUnitDatabaseInterface
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    protected $storageAliasGenerator;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(\Doctrine\DBAL\Connection $connection, StorageAliasGenerator $storageAliasGenerator,
        ExceptionLoggerInterface $exceptionLogger)
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     *
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator
     */
    public function getStorageAliasGenerator()
    {
        return $this->storageAliasGenerator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator $storageAliasGenerator
     */
    public function setStorageAliasGenerator($storageAliasGenerator)
    {
        $this->storageAliasGenerator = $storageAliasGenerator;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    public function getExceptionLogger()
    {
        return $this->exceptionLogger;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function setExceptionLogger($exceptionLogger)
    {
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     *
     * @param \Exception $exception
     */
    public function handleError(\Exception $exception)
    {
        $this->getExceptionLogger()->logException(
            new \Exception('[Message: ' . $exception->getMessage() . '] [Information: {USER INFO GOES HERE}]'),
            ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::create()
     */
    public function create($storageUnitName, $properties, $indexes)
    {
        try
        {
            if ($this->getConnection()->getSchemaManager()->tablesExist(array($storageUnitName)))
            {
                $this->drop($storageUnitName);
            }

            $schema = new \Doctrine\DBAL\Schema\Schema();
            $table = $schema->createTable($storageUnitName);

            foreach ($properties as $property => $attributes)
            {
                switch ($attributes['type'])
                {
                    case 'text' :
                        if ($attributes['length'] && $attributes['length'] <= 255)
                        {
                            $attributes['type'] = 'string';
                        }
                        break;
                }

                $type = $this->parsePropertyType($attributes);
                $options = $this->parseAttributes($attributes);

                $table->addColumn($property, $attributes['type'], $options);

                if ($options['autoincrement'] == true)
                {
                    $primaryKeyName = $this->getStorageAliasGenerator()->getConstraintName(
                        $storageUnitName,
                        $storageUnitName,
                        StorageAliasGenerator::TYPE_CONSTRAINT);

                    $table->setPrimaryKey(array($property), $primaryKeyName);
                }
            }

            foreach ($indexes as $index => $attributes)
            {
                $indexName = $this->getStorageAliasGenerator()->getConstraintName(
                    $storageUnitName,
                    $index,
                    StorageAliasGenerator::TYPE_CONSTRAINT);

                switch ($attributes['type'])
                {
                    case 'primary' :
                        $table->setPrimaryKey(array_keys($attributes['fields']), $indexName);
                        break;
                    case 'unique' :
                        $table->addUniqueIndex(array_keys($attributes['fields']), $indexName);
                        break;
                    default :
                        $table->addIndex(array_keys($attributes['fields']), $indexName);
                        break;
                }
            }

            foreach ($schema->toSql($this->getConnection()->getDatabasePlatform()) as $query)
            {
                $statement = $this->getConnection()->query($query);

                if ($statement instanceof \PDOException)
                {
                    $this->handleError($statement);

                    return false;
                }
            }

            return true;
        }
        catch (\Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::exists()
     */
    public function exists($storageUnitName)
    {
        return $this->getConnection()->getSchemaManager()->tablesExist(array($storageUnitName));
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::drop()
     */
    public function drop($storageUnitName)
    {
        $schema = new \Doctrine\DBAL\Schema\Schema(array(new \Doctrine\DBAL\Schema\Table($storageUnitName)));

        $newSchema = clone $schema;
        $newSchema->dropTable($storageUnitName);

        $sql = $schema->getMigrateToSql($newSchema, $this->getConnection()->getDatabasePlatform());

        foreach ($sql as $query)
        {
            $statement = $this->getConnection()->query($query);

            if ($statement instanceof \PDOException)
            {
                $this->handleError($statement);

                return false;
            }
        }

        return true;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::rename()
     */
    public function rename($oldStorageUnitName, $newStorageUnitName)
    {
        $query = 'ALTER TABLE ' . $oldStorageUnitName . ' RENAME TO ' . $newStorageUnitName;

        $statement = $this->getConnection()->query($query);

        if (! $statement instanceof \PDOException)
        {
            return true;
        }
        else
        {
            $this->handleError($statement);

            return false;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::alter()
     */
    public function alter($type, $storageUnitName, $property, $attributes = array())
    {
        try
        {
            if ($type == StorageUnitRepository::ALTER_STORAGE_UNIT_DROP)
            {
                $column = new \Doctrine\DBAL\Schema\Column($property);
                $query = 'ALTER TABLE ' . handleError . ' DROP COLUMN ' .
                     $column->getQuotedName($this->getConnection()->getDatabasePlatform());
            }
            else
            {
                $column = new \Doctrine\DBAL\Schema\Column(
                    $property,
                    \Doctrine\DBAL\Types\Type::getType($this->parsePropertyType($attributes)),
                    $this->parseAttributes($attributes));

                // Column declaration translation-code more or less directly from Doctrine since it doesn't support
                // altering tables (yet)
                $columns = array();
                $columnData = array();

                if (isset($attributes['name']) && $type == StorageUnitRepository::ALTER_STORAGE_UNIT_CHANGE)
                {
                    $oldName = $column->getQuotedName($this->getConnection()->getDatabasePlatform());
                    $columnData['name'] = $oldName . ' ' . $attributes['name'];
                }
                elseif ($type == StorageUnitRepository::ALTER_STORAGE_UNIT_CHANGE)
                {
                    $name = $column->getQuotedName($this->getConnection()->getDatabasePlatform());
                    $columnData['name'] = $name . ' ' . $name;
                }
                elseif ($type == StorageUnitRepository::ALTER_STORAGE_UNIT_ADD)
                {
                    $name = $column->getQuotedName($this->getConnection()->getDatabasePlatform());
                    $columnData['name'] = $name;
                }

                $columnData['type'] = $column->getType();
                $columnData['length'] = $column->getLength();
                $columnData['notnull'] = $column->getNotNull();
                $columnData['fixed'] = $column->getFixed();
                $columnData['unique'] = false;
                $columnData['version'] = ($column->hasPlatformOption("version")) ? $column->getPlatformOption('version') : false;

                if (strtolower($columnData['type']) == "string" && $columnData['length'] === null)
                {
                    $columnData['length'] = 255;
                }

                $columnData['unsigned'] = $column->getUnsigned();
                $columnData['precision'] = $column->getPrecision();
                $columnData['scale'] = $column->getScale();
                $columnData['default'] = $column->getDefault();
                $columnData['columnDefinition'] = $column->getColumnDefinition();
                $columnData['autoincrement'] = $column->getAutoincrement();

                $columnData['comment'] = $column->getComment();
                if ($this->getConnection()->getDatabasePlatform()->isCommentedDoctrineType($column->getType()))
                {
                    $columnData['comment'] .= $this->getConnection()->getDatabasePlatform()->getDoctrineTypeComment(
                        $column->getType());
                }

                $columns = array($columnData['name'] => $columnData);

                $fieldsQuery = $this->getConnection()->getDatabasePlatform()->getColumnDeclarationListSQL($columns);

                $action = $type == StorageUnitRepository::ALTER_STORAGE_UNIT_CHANGE ? 'CHANGE' : 'ADD';
                $query = 'ALTER TABLE ' . handleError . ' ' . $action . ' COLUMN ' . $fieldsQuery;

                if ($type == StorageUnitRepository::ALTER_STORAGE_UNIT_ADD && $columnData['autoincrement'])
                {
                    $query .= ', ADD PRIMARY KEY(' . $columnData['name'] . ')';
                }
            }

            $statement = $this->getConnection()->query($query);

            if (! $statement instanceof \PDOException)
            {
                return true;
            }
            else
            {
                $this->handleError($statement);

                return false;
            }
        }
        catch (\Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::alterIndex()
     */
    public function alterIndex($type, $storageUnitName, $name = null, $columns = array())
    {
        $query = 'ALTER TABLE ' . $storageUnitName . ' ';

        switch ($type)
        {
            case StorageUnitRepository::ALTER_STORAGE_UNIT_DROP_PRIMARY_KEY :
                $query .= 'DROP PRIMARY KEY';
                break;
            case StorageUnitRepository::ALTER_STORAGE_UNIT_DROP_INDEX :

                if (is_null($name))
                {
                    return false;
                }

                $query .= 'DROP INDEX ' . $name;
                break;
            case StorageUnitRepository::ALTER_STORAGE_UNIT_ADD_PRIMARY_KEY :
                $query .= 'ADD PRIMARY KEY(' . implode(', ', array_unique($columns)) . ')';
                break;
            case StorageUnitRepository::ALTER_STORAGE_UNIT_ADD_INDEX :
                $query .= 'ADD ' . $this->getConnection()->getDatabasePlatform()->getIndexDeclarationSQL(
                    $name,
                    new \Doctrine\DBAL\Schema\Index($name, $columns, false, false));
                break;
            case StorageUnitRepository::ALTER_STORAGE_UNIT_ADD_UNIQUE :
                $query .= 'ADD ' . $this->getConnection()->getDatabasePlatform()->getIndexDeclarationSQL(
                    $name,
                    new \Doctrine\DBAL\Schema\Index($name, $columns, true, false));
                break;
        }

        $statement = $this->getConnection()->query($query);

        if (! $statement instanceof \PDOException)
        {
            return true;
        }
        else
        {
            $this->handleError($statement);

            return false;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::truncate()
     */
    public function truncate($tableName, $optimize = true)
    {
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        $queryBuilder->delete($tableName);

        $statement = $this->getConnection()->query($queryBuilder->getSQL());

        if (! $statement instanceof \PDOException)
        {
            if ($optimize)
            {
                return $this->optimize($tableName);
            }
            else
            {
                return true;
            }
        }
        else
        {
            $this->handleError($statement);

            return false;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface::optimize()
     */
    public function optimize($storageUnitName)
    {
        return true;
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }

    /**
     *
     * @param string[] $attributes
     *
     * @return string
     */
    public static function parsePropertyType($attributes)
    {
        switch ($attributes['type'])
        {
            case 'text' :
                if ($attributes['length'] && $attributes['length'] <= 255)
                {
                    return 'string';
                }
                else
                {
                    return $attributes['type'];
                }
                break;
            case 'integer' :
                if (is_null($attributes['length']))
                {
                    return 'integer';
                }
                elseif ($attributes['length'] <= 2)
                {
                    return 'smallint';
                }
                elseif ($attributes['length'] <= 9)
                {
                    return 'integer';
                }
                else
                {
                    return 'bigint';
                }
            default :
                return $attributes['type'];
                break;
        }
    }

    /**
     *
     * @param string[] $attributes
     *
     * @return string[]
     */
    public static function parseAttributes($attributes)
    {
        $options = array();

        foreach ($attributes as $attribute => $value)
        {
            switch ($attribute)
            {
                case 'length' :
                    $options[$attribute] = (is_numeric($value) ? (int) $value : null);
                    break;
                case 'unsigned' :
                    $options[$attribute] = ($value == 1 ? true : false);
                    break;
                case 'fixed' :
                    if ($attributes['type'] != 'string')
                    {
                        $options[$attribute] = ($value == 1 ? true : false);
                    }
                    break;
                case 'notnull' :
                    $options[$attribute] = ($value == 1 ? true : false);
                    break;
                case 'default' :
                    $options[$attribute] = (! is_numeric($value) && empty($value) ? null : $value);
                    break;
                case 'autoincrement' :
                    $options[$attribute] = ($value == 'true' ? true : false);
                    break;
            }
        }

        if (! isset($options['notnull']))
        {
            $options['notnull'] = false;
        }

        return $options;
    }
}
