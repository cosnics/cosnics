<?php
namespace Chamilo\Libraries\Protocol\Session\Factory;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

/**
 * @package Chamilo\Libraries\Protocol\Session\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class PdoSessionHandlerFactory
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPdoSessionHandler(): PdoSessionHandler
    {
        return new PdoSessionHandler($this->getConnection()->getNativeConnection(), [
            'db_table' => 'user_session',
            'db_id_col' => 'session_id',
            'db_data_col' => 'data',
            'db_lifetime_col' => 'lifetime',
            'db_time_col' => 'modified'
        ]);
    }
}

