<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Database;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Traits\ClassContext;
use Chamilo\Libraries\Storage\DataManager\Interfaces\StorageUnitDatabaseInterface;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Storage\DataManager\StorageAliasGenerator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Exception;

/**
 * This class provides basic functionality for storage unit manipulations via Doctrine
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class StorageUnitDatabase implements StorageUnitDatabaseInterface
{
    use ClassContext;

    protected Connection $connection;

    protected ExceptionLoggerInterface $exceptionLogger;

    protected StorageAliasGenerator $storageAliasGenerator;

    public function __construct(
        Connection $connection, StorageAliasGenerator $storageAliasGenerator, ExceptionLoggerInterface $exceptionLogger
    )
    {
        $this->connection = $connection;
        $this->storageAliasGenerator = $storageAliasGenerator;
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     * @param string[] $attributes
     */
    public function alter(int $type, string $storageUnitName, string $property, array $attributes = []): bool
    {
        try
        {
            if ($type == StorageUnitRepository::ALTER_STORAGE_UNIT_DROP)
            {
                $column = new Column($property, Type::getType($this::parsePropertyType($attributes)));
                $query = 'ALTER TABLE ' . $storageUnitName . ' DROP COLUMN ' .
                    $column->getQuotedName($this->getConnection()->getDatabasePlatform());
            }
            else
            {
                $column = new Column(
                    $property, Type::getType($this::parsePropertyType($attributes)), $this::parseAttributes($attributes)
                );

                // Column declaration translation-code more or less directly from Doctrine since it doesn't support
                // altering tables (yet)
                $columnData = [];

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
                $columnData['notnull'] = $column->getNotnull();
                $columnData['fixed'] = $column->getFixed();
                $columnData['unique'] = false;
                $columnData['version'] =
                    ($column->hasPlatformOption('version')) ? $column->getPlatformOption('version') : false;

                if (strtolower($columnData['type']->getName()) == 'string' && $columnData['length'] === null)
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
                if ($column->getType()->requiresSQLCommentHint($this->getConnection()->getDatabasePlatform()))
                {
                    $columnData['comment'] .= $this->getConnection()->getDatabasePlatform()->getDoctrineTypeComment(
                        $column->getType()
                    );
                }

                $columns = [$columnData['name'] => $columnData];

                $fieldsQuery = $this->getConnection()->getDatabasePlatform()->getColumnDeclarationListSQL($columns);

                $action = $type == StorageUnitRepository::ALTER_STORAGE_UNIT_CHANGE ? 'CHANGE' : 'ADD';
                $query = 'ALTER TABLE ' . $storageUnitName . ' ' . $action . ' COLUMN ' . $fieldsQuery;

                if ($type == StorageUnitRepository::ALTER_STORAGE_UNIT_ADD && $columnData['autoincrement'])
                {
                    $query .= ', ADD PRIMARY KEY(' . $columnData['name'] . ')';
                }
            }

            $this->getConnection()->executeQuery($query);

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    public function alterIndex(int $type, string $storageUnitName, ?string $indexName = null, array $columns = []): bool
    {
        try
        {
            $query = 'ALTER TABLE ' . $storageUnitName . ' ';

            switch ($type)
            {
                case StorageUnitRepository::ALTER_STORAGE_UNIT_DROP_PRIMARY_KEY :
                    $query .= 'DROP PRIMARY KEY';
                    break;
                case StorageUnitRepository::ALTER_STORAGE_UNIT_DROP_INDEX :

                    if (is_null($indexName))
                    {
                        return false;
                    }

                    $query .= 'DROP INDEX ' . $indexName;
                    break;
                case StorageUnitRepository::ALTER_STORAGE_UNIT_ADD_PRIMARY_KEY :
                    $query .= 'ADD PRIMARY KEY(' . implode(', ', array_unique($columns)) . ')';
                    break;
                case StorageUnitRepository::ALTER_STORAGE_UNIT_ADD_INDEX :
                    $query .= 'ADD ' . $this->getConnection()->getDatabasePlatform()->getIndexDeclarationSQL(
                            $indexName, new Index($indexName, $columns, false, false)
                        );
                    break;
                case StorageUnitRepository::ALTER_STORAGE_UNIT_ADD_UNIQUE :
                    $query .= 'ADD ' . $this->getConnection()->getDatabasePlatform()->getIndexDeclarationSQL(
                            $indexName, new Index($indexName, $columns, true, false)
                        );
                    break;
            }

            $this->getConnection()->executeQuery($query);

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    /**
     * @param string[][] $properties
     * @param string[][][] $indexes
     */
    public function create(string $storageUnitName, array $properties = [], array $indexes = []): bool
    {
        try
        {
            if ($this->getConnection()->createSchemaManager()->tablesExist([$storageUnitName]))
            {
                $this->drop($storageUnitName);
            }

            $schema = new Schema();
            $table = $schema->createTable($storageUnitName);

            foreach ($properties as $property => $attributes)
            {
                if ($attributes['type'] == 'text')
                {
                    if ($attributes['length'] && $attributes['length'] <= 255)
                    {
                        $attributes['type'] = 'string';
                    }
                }

                $options = $this::parseAttributes($attributes);

                $table->addColumn($property, $attributes['type'], $options);

                if ($options['autoincrement'])
                {
                    $primaryKeyName = $this->getStorageAliasGenerator()->getConstraintName(
                        $storageUnitName, $storageUnitName
                    );

                    $table->setPrimaryKey([$property], $primaryKeyName);
                }
            }

            foreach ($indexes as $index => $attributes)
            {
                $indexName = $this->getStorageAliasGenerator()->getConstraintName(
                    $storageUnitName, $index
                );

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
                $this->getConnection()->executeQuery($query);
            }

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    public function drop(string $storageUnitName): bool
    {
        try
        {
            $schema = new Schema([new Table($storageUnitName)]);

            $newSchema = clone $schema;
            $newSchema->dropTable($storageUnitName);

            $schemaDiff = (new Comparator())->compareSchemas($schema, $newSchema);

            $sql = $schemaDiff->toSql($this->getConnection()->getDatabasePlatform());

            foreach ($sql as $query)
            {
                $this->getConnection()->executeQuery($query);
            }

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    public function exists(string $storageUnitName): bool
    {
        try
        {
            return $this->getConnection()->createSchemaManager()->tablesExist([$storageUnitName]);
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getExceptionLogger(): ExceptionLoggerInterface
    {
        return $this->exceptionLogger;
    }

    public function getStorageAliasGenerator(): StorageAliasGenerator
    {
        return $this->storageAliasGenerator;
    }

    public function handleError(Exception $exception)
    {
        $this->getExceptionLogger()->logException(
            new Exception('[Message: ' . $exception->getMessage() . '] [Information: {USER INFO GOES HERE}]'),
            ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR
        );
    }

    public function optimize(string $storageUnitName): bool
    {
        return true;
    }

    /**
     * @throws \ReflectionException
     */
    public static function package(): string
    {
        return ClassnameUtilities::getInstance()->getNamespaceParent(static::context(), 3);
    }

    /**
     *
     * @param string[] $attributes
     *
     * @return string[]
     */
    public static function parseAttributes(array $attributes = []): array
    {
        $options = [];

        foreach ($attributes as $attribute => $value)
        {
            switch ($attribute)
            {
                case 'length' :
                    $options[$attribute] = (is_numeric($value) ? (int) $value : null);
                    break;
                case 'fixed' :
                    if ($attributes['type'] != 'string')
                    {
                        $options[$attribute] = $value == 1;
                    }
                    break;
                case 'unsigned' :
                case 'notnull' :
                    $options[$attribute] = $value == 1;
                    break;
                case 'default' :
                    $options[$attribute] = (!is_numeric($value) && empty($value) ? null : $value);
                    break;
                case 'autoincrement' :
                    $options[$attribute] = $value == 'true';
                    break;
            }
        }

        if (!isset($options['notnull']))
        {
            $options['notnull'] = false;
        }

        return $options;
    }

    /**
     *
     * @param string[] $attributes
     */
    public static function parsePropertyType(array $attributes = []): string
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
        }
    }

    public function rename(string $oldStorageUnitName, string $newStorageUnitName): bool
    {
        try
        {
            $query = 'ALTER TABLE ' . $oldStorageUnitName . ' RENAME TO ' . $newStorageUnitName;

            $this->getConnection()->executeQuery($query);

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }

    public function truncate(string $storageUnitName, ?bool $optimize = true): bool
    {
        try
        {
            $queryBuilder = $this->getConnection()->createQueryBuilder();
            $queryBuilder->delete($storageUnitName);

            $this->getConnection()->executeQuery($queryBuilder->getSQL());

            if ($optimize)
            {
                return $this->optimize($storageUnitName);
            }

            return true;
        }
        catch (Exception $exception)
        {
            $this->handleError($exception);

            return false;
        }
    }
}
