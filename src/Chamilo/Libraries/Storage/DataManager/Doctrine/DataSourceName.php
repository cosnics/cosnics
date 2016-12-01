<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine;

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
     *
     * @see \Chamilo\Libraries\Storage\DataManager\DataSourceName::get_implemented_driver()
     */
    public function get_implemented_driver()
    {
        switch ($this->get_driver())
        {
            case self::DRIVER_OCI8 :
                return 'Doctrine\DBAL\Driver\OCI8\Driver';
                break;
            case self::DRIVER_PGSQL :
                return 'Doctrine\DBAL\Driver\PDOPgSql\Driver';
                break;
            case self::DRIVER_SQLITE :
                return 'Doctrine\DBAL\Driver\PDOSqlite\Driver';
                break;
            case self::DRIVER_MYSQL :
                return 'Doctrine\DBAL\Driver\PDOMySql\Driver';
                break;
            case self::DRIVER_MSSQL :
                return 'Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql\MsSqlDoctrineDriver';
                break;
            case self::DRIVER_IBM_DB2 :
                return 'Doctrine\DBAL\Driver\IBMDB2\Driver';
                break;
            case self::DRIVER_IBM :
                return 'Doctrine\DBAL\Driver\PDOIbm\Driver';
                break;
            case self::DRIVER_OCI :
                throw new \Exception(
                    'The requested driver (' . $this->get_driver() .
                         ') is not available in Doctrine. Please provide a driver for Doctrine or choose another implementation');
                break;
            // Deprecated option for backwards compatibility with older configuration files
            case 'mysqli' :
                return 'Doctrine\DBAL\Driver\PDOMySql\Driver';
                break;
            default :
                throw new \Exception(
                    'The requested driver (' . $this->get_driver() .
                         ') is not available in Doctrine. Please provide a driver for Doctrine or choose another implementation');
                break;
        }
    }
}
