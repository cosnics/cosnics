<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Core\User\Storage\DataClass\Session;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

/**
 * @package Chamilo\Libraries\Platform\Session
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

    public function getPdoSessionHandler(): PdoSessionHandler
    {
        return new PdoSessionHandler($this->getConnection()->getNativeConnection(), [
            'db_table' => Session::getStorageUnitName(),
            'db_id_col' => Session::PROPERTY_SESSION_ID,
            'db_data_col' => Session::PROPERTY_DATA,
            'db_lifetime_col' => Session::PROPERTY_LIFETIME,
            'db_time_col' => Session::PROPERTY_MODIFIED
        ]);
    }
}

