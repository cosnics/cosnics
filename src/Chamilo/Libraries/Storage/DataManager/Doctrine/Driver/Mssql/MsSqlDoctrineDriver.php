<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MsSqlDoctrineDriver implements \Doctrine\DBAL\Driver
{

    /**
     * Attempts to establish a connection with the underlying driver.
     *
     * @param string[] $params
     * @param string $username
     * @param string $password
     * @param string[] $driverOptions
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql\MsSqlDoctrinePdoConnection
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        return new MsSqlDoctrinePdoConnection($this->_constructPdoDsn($params), $username, $password, $driverOptions);
    }

    /**
     * Constructs the MsSql PDO DSN.
     *
     * @return string The DSN.
     */
    private function _constructPdoDsn(array $params)
    {
        if (extension_loaded('pdo_dblib'))
        {
            $dsn = 'dblib:';
            if (isset($params['host']) && $params['host'] != '')
            {
                $dsn .= 'host=' . $params['host'] . '; ';
            }

            if (isset($params['dbname']))
            {
                $dsn .= 'dbname=' . $params['dbname'] . ';';
            }
        }
        elseif (extension_loaded('pdo_sqlsrv'))
        {
            $dsn = 'sqlsrv:Server=' . $params['host'] . ';Database=' . $params['dbname'];
        }
        return $dsn;
    }

    /**
     *
     * @see \Doctrine\DBAL\Driver::getDatabasePlatform()
     */
    public function getDatabasePlatform()
    {
        return new \Doctrine\DBAL\Platforms\SQLServer2008Platform();
    }

    /**
     *
     * @see \Doctrine\DBAL\Driver::getSchemaManager()
     */
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        return new \Doctrine\DBAL\Schema\SQLServerSchemaManager($conn);
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
     *
     * @see \Doctrine\DBAL\Driver::getDatabase()
     */
    public function getDatabase(\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();
        return $params['dbname'];
    }
}
