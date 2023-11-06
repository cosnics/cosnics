<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Queue\Service\Producer\BeanstalkProducer;
use Chamilo\Core\Queue\Service\Producer\DBALProducer;
use Doctrine\DBAL\Connection;
use Enqueue\Dbal\DbalContext;
use Enqueue\Pheanstalk\PheanstalkConnectionFactory;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PsrContextFactory
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
     * @param \Doctrine\DBAL\Connection $dbalConnection
     */
    public function __construct(ConfigurationConsulter $configurationConsulter, Connection $dbalConnection)
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->dbalConnection = $dbalConnection;
    }

    /**
     * @return \Interop\Queue\PsrContext
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
                return new DbalContext($this->dbalConnection, ['table_name' => 'queue_queue_php8']);
        }
    }
}