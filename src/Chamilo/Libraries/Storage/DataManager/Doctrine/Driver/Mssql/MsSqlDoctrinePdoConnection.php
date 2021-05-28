<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql;

use Doctrine\DBAL\Driver\PDO\Connection;
use Doctrine\DBAL\Driver\PDO\SQLSrv\Statement;
use Doctrine\DBAL\Driver\Result;
use PDO;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Driver\Mssql
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MsSqlDoctrinePdoConnection extends Connection
{
    /**
     * {@inheritdoc}
     *
     * @param string $dsn
     * @param string|null $user
     * @param string|null $password
     * @param mixed[]|null $options
     *
     * @internal The connection can be only instantiated by its driver.
     *
     */
    public function __construct($dsn, $user = null, $password = null, ?array $options = null)
    {
        parent::__construct($dsn, $user, $password, $options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, [Statement::class, []]);
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId($name = null)
    {
        if ($name === null)
        {
            return parent::lastInsertId($name);
        }

        $stmt = $this->prepare('SELECT CONVERT(VARCHAR(MAX), current_value) FROM sys.sequences WHERE name = ?');
        $stmt->execute([$name]);

        if ($stmt instanceof Result)
        {
            return $stmt->fetchOne();
        }

        return $stmt->fetchColumn();
    }

    public function query(...$args)
    {
        $statement = parent::query(...$args);

        if (!extension_loaded('pdo_dblib') && extension_loaded('pdo_sqlsrv'))
        {
            $statement->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
        }

        return $statement;
    }
}
