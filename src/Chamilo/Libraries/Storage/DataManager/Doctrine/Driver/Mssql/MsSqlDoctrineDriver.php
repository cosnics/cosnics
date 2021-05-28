<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql;

use Doctrine\DBAL\Driver\AbstractSQLServerDriver;
use Doctrine\DBAL\Driver\AbstractSQLServerDriver\Exception\PortWithoutHost;
use Doctrine\Deprecations\Deprecation;
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
     * Constructs the Sqlsrv PDO DSN.
     *
     * @param mixed[]  $params
     * @param string[] $connectionOptions
     *
     * @return string The DSN.
     */
    private function _constructPdoDsn(array $params, array $connectionOptions)
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

            if (isset($params['charset']))
            {
                $dsn .= 'charset=' . $params['charset'] . ';';
            }
        }
        elseif (extension_loaded('pdo_sqlsrv'))
        {
            $dsn = 'sqlsrv:server=';

            if (isset($params['host']))
            {
                $dsn .= $params['host'];

                if (isset($params['port']))
                {
                    $dsn .= ',' . $params['port'];
                }
            }
            elseif (isset($params['port']))
            {
                throw PortWithoutHost::new();
            }

            if (isset($params['dbname']))
            {
                $connectionOptions['Database'] = $params['dbname'];
            }

            if (isset($params['MultipleActiveResultSets']))
            {
                $connectionOptions['MultipleActiveResultSets'] = $params['MultipleActiveResultSets'] ? 'true' : 'false';
            }

            $dsn .= $this->getConnectionOptionsDsn($connectionOptions);

            var_dump($dsn);
        }
        else
        {
            throw new Exception('No valid MsSQL extension available, please configure pdo_dblib or pdo_sqlsrv.');
        }

        return $dsn;
    }

    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        $pdoOptions = $dsnOptions = [];

        foreach ($driverOptions as $option => $value)
        {
            if (is_int($option))
            {
                $pdoOptions[$option] = $value;
            }
            else
            {
                $dsnOptions[$option] = $value;
            }
        }

        return new MsSqlDoctrinePdoConnection(
            $this->_constructPdoDsn($params, $dsnOptions), $username, $password, $pdoOptions
        );
    }

    /**
     * Converts a connection options array to the DSN
     *
     * @param string[] $connectionOptions
     */
    private function getConnectionOptionsDsn(array $connectionOptions): string
    {
        $connectionOptionsDsn = '';

        foreach ($connectionOptions as $paramName => $paramValue)
        {
            $connectionOptionsDsn .= sprintf(';%s=%s', $paramName, $paramValue);
        }

        return $connectionOptionsDsn;
    }

    /**
     *
     * @see \Doctrine\DBAL\Driver::getName()
     */
    public function getName()
    {
        Deprecation::trigger(
            'doctrine/dbal', 'https://github.com/doctrine/dbal/issues/3580', 'Driver::getName() is deprecated'
        );

        return 'pdo_mssql';
    }
}
