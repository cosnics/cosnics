<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql;

use Doctrine\DBAL\Driver\AbstractSQLServerDriver;

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
     * Attempts to establish a connection with the underlying driver.
     *
     * @param string[] $parameters
     * @param string $username
     * @param string $password
     * @param string[] $driverOptions
     * @return \Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql\MsSqlDoctrinePdoConnection
     */
    public function connect(array $parameters, $username = null, $password = null, array $driverOptions = array())
    {
        return new MsSqlDoctrinePdoConnection($this->_constructPdoDsn($parameters), $username, $password, $driverOptions);
    }

    /**
     * Constructs the MsSql PDO DSN.
     *
     * @param string[] $parameters
     * @return string The DSN.
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

        return $dsn;
    }

    /**
     *
     * @see \Doctrine\DBAL\Driver::getName()
     */
    public function getName()
    {
        return 'pdo_mssql';
    }
}
