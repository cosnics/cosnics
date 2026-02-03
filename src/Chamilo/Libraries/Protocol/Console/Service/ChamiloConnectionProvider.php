<?php
namespace Chamilo\Libraries\Protocol\Console\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;

/**
 * @package Chamilo\Libraries\Protocol\Console\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ChamiloConnectionProvider implements ConnectionProvider
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(string $name): Connection
    {
        return $this->connection;
    }

    public function getDefaultConnection(): Connection
    {
        return $this->connection;
    }
}