<?php

namespace Chamilo\Core\Queue\Test\Unit\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Queue\Service\PsrContextFactory;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Doctrine\DBAL\Connection;
use Enqueue\Dbal\DbalContext;
use Enqueue\Pheanstalk\PheanstalkContext;

/**
 * Tests the PsrContextFactory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PsrContextFactoryTest extends ChamiloTestCase
{
    /**
     * @var PsrContextFactory
     */
    protected $psrContextFactory;

    /**
     * @var \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configurationConsulterMock;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connectionMock;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->configurationConsulterMock = $this->getMockBuilder(ConfigurationConsulter::class)
            ->disableOriginalConstructor()->getMock();

        $this->connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()->getMock();

        $this->psrContextFactory = new PsrContextFactory($this->configurationConsulterMock, $this->connectionMock);
    }

    /**
     * Tear down after each test
     */
    public function tearDown(): void
    {
        unset($this->psrContextFactory);
    }

    public function testDefaultFactory()
    {
        $context = $this->psrContextFactory->createContext();
        $this->assertInstanceOf(DbalContext::class, $context);
    }

    public function testDBalFactory()
    {
        $this->configurationConsulterMock->expects($this->once())
            ->method('getSetting')
            ->with(['Chamilo\Core\Queue', 'queue_provider'])
            ->will($this->returnValue('database'));

        $context = $this->psrContextFactory->createContext();
        $this->assertInstanceOf(DbalContext::class, $context);
    }

    public function testBeanstalkFactory()
    {
        $this->configurationConsulterMock->expects($this->at(0))
            ->method('getSetting')
            ->with(['Chamilo\Core\Queue', 'queue_provider'])
            ->will($this->returnValue('beanstalk'));

        $this->configurationConsulterMock->expects($this->at(1))
            ->method('getSetting')
            ->with(['Chamilo\Core\Queue', 'beanstalk_queue_host'])
            ->will($this->returnValue('localhost'));

        $this->configurationConsulterMock->expects($this->at(2))
            ->method('getSetting')
            ->with(['Chamilo\Core\Queue', 'beanstalk_queue_port'])
            ->will($this->returnValue('2846'));

        $context = $this->psrContextFactory->createContext();
        $this->assertInstanceOf(PheanstalkContext::class, $context);
    }
}

