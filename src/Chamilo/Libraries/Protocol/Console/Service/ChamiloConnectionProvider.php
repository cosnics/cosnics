<?php
namespace Chamilo\Libraries\Console\Command\Vendor\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\Console\ConnectionProvider;

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