<?php
namespace Chamilo\Core\User\Factory;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\User\Storage\DataClass\Session;
use Doctrine\DBAL\Connection;
use SessionHandlerInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

/**
 * @package Chamilo\Core\User\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SessionHandlerFactory
{

    private string $configuredSessionHandler;

    private Connection $connection;

    private FileConfigurationLocator $fileConfigurationLocator;

    public function __construct(
        FileConfigurationLocator $fileConfigurationLocator, string $configuredSessionHandler, Connection $connection
    )
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
        $this->configuredSessionHandler = $configuredSessionHandler;
        $this->connection = $connection;
    }

    public function getConfiguredSessionHandler(): string
    {
        return $this->configuredSessionHandler;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }

    public function getSessionHandler(): ?SessionHandlerInterface
    {
        if ($this->getFileConfigurationLocator()->isAvailable())
        {
            if ($this->getConfiguredSessionHandler() == 'chamilo')
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

        return null;
    }
}

