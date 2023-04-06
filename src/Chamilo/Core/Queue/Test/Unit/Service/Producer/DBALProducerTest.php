<?php

namespace Chamilo\Core\Queue\Test\Unit\Service\Producer;

use Chamilo\Core\Queue\Service\Producer\DBALProducer;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Enqueue\Dbal\DbalContext;
use Enqueue\Dbal\DbalMessage;
use Interop\Queue\PsrProducer;
use Interop\Queue\PsrQueue;

/**
 * Tests the DBALProducer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DBALProducerTest extends ChamiloTestCase
{
    /**
     * @var DBALProducer
     */
    protected $DBALProducer;

    /**
     * @var \Enqueue\Dbal\DbalContext|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $dbalContextMock;

    /**
     * Setup before each test
     */
    protected function setUp(): void    {
        $this->dbalContextMock = $this->getMockBuilder(DbalContext::class)
            ->disableOriginalConstructor()->getMock();

        $this->DBALProducer = new DBALProducer($this->dbalContextMock);
    }

    /**
     * Tear down after each test
     */
    protected function tearDown(): void    {
        unset($this->dbalContextMock);
        unset($this->DBALProducer);
    }

    public function testProduceMessage()
    {
        $psrQueue = $this->getMockBuilder(PsrQueue::class)
            ->disableOriginalConstructor()->getMock();

        $psrMessage = $this->getMockBuilder(DbalMessage::class)
            ->disableOriginalConstructor()->getMock();

        $psrProducer = $this->getMockBuilder(PsrProducer::class)
            ->disableOriginalConstructor()->getMock();

        $this->dbalContextMock->expects($this->once())
            ->method('createQueue')
            ->with('notifications')
            ->will($this->returnValue($psrQueue));

        $this->dbalContextMock->expects($this->once())
            ->method('createMessage')
            ->with('test')
            ->will($this->returnValue($psrMessage));

        $psrMessage->expects($this->once())
            ->method('setDeliveryDelay')
            ->with(500000);

        $this->dbalContextMock->expects($this->once())
            ->method('createProducer')
            ->will($this->returnValue($psrProducer));

        $psrProducer->expects($this->once())
            ->method('send')
            ->with($psrQueue, $psrMessage);

        $this->DBALProducer->produceMessage('test', 'notifications', 500);
    }
}

