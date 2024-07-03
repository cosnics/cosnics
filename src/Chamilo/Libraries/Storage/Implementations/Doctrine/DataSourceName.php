<?php
namespace Chamilo\Libraries\Storage\Implementations\Doctrine;

use Exception;

/**
 * @package Chamilo\Libraries\Storage\Implementations\Doctrine
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataSourceName extends \Chamilo\Libraries\Storage\DataSourceName
{

    /**
     * @throws \Exception
     */
    public function getImplementedDriver(): string
    {
        return match ($this->getDriver())
        {
            self::DRIVER_OCI8 => 'Doctrine\DBAL\Driver\OCI8\Driver',
            self::DRIVER_PGSQL => 'Doctrine\DBAL\Driver\PDO\PgSQL\Driver',
            self::DRIVER_SQLITE => 'Doctrine\DBAL\Driver\PDO\SQLite\Driver',
            self::DRIVER_MYSQL => 'Doctrine\DBAL\Driver\PDO\MySQL\Driver',
            self::DRIVER_MSSQL => 'Doctrine\DBAL\Driver\PDO\SQLSrv\Driver',
            self::DRIVER_IBM_DB2 => 'Doctrine\DBAL\Driver\IBMDB2\Driver',
            self::DRIVER_IBM => 'Doctrine\DBAL\Driver\PDOIbm\Driver',
            default => throw new Exception(
                'The requested driver (' . $this->getDriver() .
                ') is not available in Doctrine. Please provide a driver for Doctrine or choose another implementation'
            ),
        };
    }
}
