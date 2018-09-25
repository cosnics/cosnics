<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Doctrine\DBAL\Driver\Connection;
use Enqueue\Dbal\DbalConnectionFactory;
use Enqueue\Pheanstalk\PheanstalkConnectionFactory;
use Interop\Queue\PsrConnectionFactory;
use Interop\Queue\PsrContext;

class ConnectionFactory implements PsrConnectionFactory
{
    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $dbalConnection;

    /**
     * QueueConnectionFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Doctrine\DBAL\Driver\Connection $dbalConnection
     */
    public function __construct(ConfigurationConsulter $configurationConsulter, Connection $dbalConnection)
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->dbalConnection = $dbalConnection;
    }

    /**
     * @return PsrContext
     */
    public function createContext()
    {
        $queueProvider = $this->configurationConsulter->getSetting(['Chamilo\Core\Queue', 'queue_provider']);
        switch($queueProvider)
        {
            case 'beanstalk':
                $host = $this->configurationConsulter->getSetting(['Chamilo\Core\Queue', 'beanstalk_queue_host']);
                $port = $this->configurationConsulter->getSetting(['Chamilo\Core\Queue', 'beanstalk_queue_port']);

                $factory = new PheanstalkConnectionFactory(
                    ['host' => $host, 'port' => $port]
                );

                return $factory->createContext();
            case 'database':
            default:
                $factory = new DbalConnectionFactory($this->dbalConnection);
                return $factory->createContext();
        }
    }
}