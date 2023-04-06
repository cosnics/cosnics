<?php

namespace Chamilo\Core\Queue\Test\Unit\Service\Producer;

use Chamilo\Core\Queue\Service\Producer\BeanstalkProducer;
use Chamilo\Core\Queue\Service\Producer\DBALProducer;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Enqueue\Dbal\DbalContext;
use Enqueue\Dbal\DbalMessage;
use Enqueue\Pheanstalk\PheanstalkContext;
use Enqueue\Pheanstalk\PheanstalkMessage;
use Interop\Queue\PsrProducer;
use Interop\Queue\PsrQueue;

/**
 * Tests the DBALProducer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BeanstalkProducerTest extends ChamiloTestCase
{
    /**
     * @var \Chamilo\Core\Queue\Service\Producer\BeanstalkProducer
     */
    protected $beanstalkProducer;

    /**
     * @var \Enqueue\Pheanstalk\PheanstalkContext|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $pheanstalkContext;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->pheanstalkContext = $this->getMockBuilder(PheanstalkContext::class)
            ->disableOriginalConstructor()->getMock();

        $this->beanstalkProducer = new BeanstalkProducer($this->pheanstalkContext);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->pheanstalkContext);
        unset($this->beanstalkProducer);
    }

    public function testProduceMessage()
    {
        $psrQueue = $this->getMockBuilder(PsrQueue::class)
            ->disableOriginalConstructor()->getMock();

        $psrMessage = $this->getMockBuilder(PheanstalkMessage::class)
            ->disableOriginalConstructor()->getMock();

        $psrProducer = $this->getMockBuilder(PsrProducer::class)
            ->disableOriginalConstructor()->getMock();

        $this->pheanstalkContext->expects($this->once())
            ->method('createQueue')
            ->with('notifications')
            ->will($this->returnValue($psrQueue));

        $this->pheanstalkContext->expects($this->once())
            ->method('createMessage')
            ->with('test')
            ->will($this->returnValue($psrMessage));

        $psrMessage->expects($this->once())
            ->method('setDelay')
            ->with(500);

        $this->pheanstalkContext->expects($this->once())
            ->method('createProducer')
            ->will($this->returnValue($psrProducer));

        $psrProducer->expects($this->once())
            ->method('send')
            ->with($psrQueue, $psrMessage);

        $this->beanstalkProducer->produceMessage('test', 'notifications', 500);
    }
}

