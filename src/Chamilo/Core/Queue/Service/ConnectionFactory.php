<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
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
     * QueueConnectionFactory constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return PsrContext
     */
    public function createContext()
    {
        $factory = new PheanstalkConnectionFactory();
        return $factory->createContext();
    }
}