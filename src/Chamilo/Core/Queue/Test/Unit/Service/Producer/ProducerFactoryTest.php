<?php

namespace Chamilo\Core\Queue\Test\Unit\Service\Producer;

use Chamilo\Core\Queue\Service\Producer\BeanstalkProducer;
use Chamilo\Core\Queue\Service\Producer\DBALProducer;
use Chamilo\Core\Queue\Service\Producer\ProducerFactory;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Mysqli\Driver;
use Enqueue\Dbal\DbalContext;
use Enqueue\Null\NullContext;
use Enqueue\Pheanstalk\PheanstalkContext;
use Pheanstalk\Pheanstalk;

/**
 * Tests the ProducerFactory
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProducerFactoryTest extends ChamiloTestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testCreateDefaultProducer()
    {
        $psrContext = new NullContext();
        $producerFactory = new ProducerFactory($psrContext);
        $producerFactory->createProducer();
    }

    public function testCreateDBALProducer()
    {
        $psrContext = new DbalContext(new Connection([], new Driver()));
        $producerFactory = new ProducerFactory($psrContext);
        $this->assertInstanceOf(DbalProducer::class, $producerFactory->createProducer());
    }

    public function testCreateBeanstalkProducer()
    {
        $psrContext = new PheanstalkContext(new Pheanstalk('localhost'));
        $producerFactory = new ProducerFactory($psrContext);
        $this->assertInstanceOf(BeanstalkProducer::class, $producerFactory->createProducer());
   }
}

