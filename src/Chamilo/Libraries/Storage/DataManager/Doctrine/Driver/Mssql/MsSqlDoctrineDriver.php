<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\AbstractSQLServerDriver;
use Doctrine\DBAL\Platforms\SQLServer2008Platform;
use Doctrine\DBAL\Schema\SQLServerSchemaManager;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MsSqlDoctrineDriver extends AbstractSQLServerDriver
{

    /**
     * Constructs the MsSql PDO DSN.
     *
     * @param string[] $parameters
     *
     * @return string The DSN.
     * @throws \Exception
     */
    private function _constructPdoDsn(array $parameters)
    {
        if (extension_loaded('pdo_dblib'))
        {
            $dsn = 'dblib:';

            if (isset($parameters['host']) && $parameters['host'] != '')
            {
                $dsn .= 'host=' . $parameters['host'] . '; ';
            }

            if (isset($parameters['dbname']))
            {
                $dsn .= 'dbname=' . $parameters['dbname'] . ';';
            }

            if (isset($parameters['charset']))
            {
                $dsn .= 'charset=' . $parameters['charset'] . ';';
            }
        }
        elseif (extension_loaded('pdo_sqlsrv'))
        {
            $dsn = 'sqlsrv:Server=' . $parameters['host'] . ';Database=' . $parameters['dbname'];
        }
        else
        {
            throw new Exception('No valid MsSQL extension available, please configure pdo_dblib or pdo_sqlsrv.');
        }

        return $dsn;
    }

    /**
     * Attempts to establish a connection with the underlying driver.
     *
     * @param string[] $parameters
     * @param string $username
     * @param string $password
     * @param string[] $driverOptions
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql\MsSqlDoctrinePdoConnection
     * @throws \Exception
     */
    public function connect(array $parameters, $username = null, $password = null, array $driverOptions = array())
    {
        return new MsSqlDoctrinePdoConnection(
            $this->_constructPdoDsn($parameters), $username, $password, $driverOptions
        );
    }

    /**
     * @param \Doctrine\DBAL\Connection $conn
     *
     * @return string
     */
    public function getDatabase(Connection $conn)
    {
        $params = $conn->getParams();

        return $params['dbname'];
    }

    /**
     * @return \Doctrine\DBAL\Platforms\SQLServer2008Platform
     */
    public function getDatabasePlatform()
    {
        return new SQLServer2008Platform();
    }

    /**
     *
     * @see \Doctrine\DBAL\Driver::getName()
     */
    public function getName()
    {
        return 'pdo_mssql';
    }

    /**
     * @param \Doctrine\DBAL\Connection $conn
     *
     * @return \Doctrine\DBAL\Schema\SQLServerSchemaManager
     */
    public function getSchemaManager(Connection $conn)
    {
        return new SQLServerSchemaManager($conn);
    }
}
