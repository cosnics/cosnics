<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DataSourceName extends \Chamilo\Libraries\Storage\DataManager\DataSourceName
{

    /**
     * @throws \Exception
     */
    public function getImplementedDriver(): string
    {
        switch ($this->getDriver())
        {
            case self::DRIVER_OCI8 :
                return 'Doctrine\DBAL\Driver\OCI8\Driver';
            case self::DRIVER_PGSQL :
                return 'Doctrine\DBAL\Driver\PDO\PgSQL\Driver';
            case self::DRIVER_SQLITE :
                return 'Doctrine\DBAL\Driver\PDO\SQLite\Driver';
            case self::DRIVER_MYSQL :
                return 'Doctrine\DBAL\Driver\PDO\MySQL\Driver';
            case self::DRIVER_MSSQL :
                return 'Doctrine\DBAL\Driver\PDO\SQLSrv\Driver';
            case self::DRIVER_IBM_DB2 :
                return 'Doctrine\DBAL\Driver\IBMDB2\Driver';
            case self::DRIVER_IBM :
                return 'Doctrine\DBAL\Driver\PDOIbm\Driver';
            default :
                throw new Exception(
                    'The requested driver (' . $this->getDriver() .
                    ') is not available in Doctrine. Please provide a driver for Doctrine or choose another implementation'
                );
        }
    }
}
