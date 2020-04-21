<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb;

use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataSourceName extends \Chamilo\Libraries\Storage\DataManager\DataSourceName
{

    /**
     *
     * @see \Chamilo\Libraries\Storage\DataManager\DataSourceName::get_implemented_driver()
     */
    public function getImplementedDriver()
    {
        switch ($this->getDriver())
        {
            case self::DRIVER_PGSQL :
                return self::DRIVER_PGSQL;
                break;
            case self::DRIVER_SQLITE :
                return self::DRIVER_SQLITE;
                break;
            case self::DRIVER_MYSQL :
            // Deprecated option for backwards compatibility with older configuration files
            case 'mysqli' :
                return self::DRIVER_MYSQL;
                break;
            case self::DRIVER_MSSQL :
                return 'sqlsrv';
                break;
            case self::DRIVER_OCI :
                return self::DRIVER_OCI;
                break;
            default :
                throw new Exception(
                    'The requested driver (' . $this->get_driver() .
                         ') is not available in ADOdb. Please provide a driver for ADOdb or choose another implementation');
                break;
        }
    }

    /**
     *
     * @return string
     */
    public function getConnectionString()
    {
        $string = array();

        $string[] = $this->getDriver(true);
        $string[] = ':';
        $string[] = 'host=' . $this->getHost();
        if ($this->getPort())
        {
            $string[] = ':';
            $string[] = $this->getPort();
        }
        $string[] = ';';
        $string[] = 'dbname=' . $this->getDatabase();
        $string[] = ';';
        $string[] = 'charset=' . $this->getCharset();

        return implode('', $string);
    }
}
