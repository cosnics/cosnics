<?php
namespace Chamilo\Core\Queue\Service\Producer;

use Enqueue\Dbal\DbalContext;

/**
 * @package Chamilo\Core\Queue\Service\Producer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DBALProducer implements ProducerInterface
{
    /**
     * @var \Enqueue\Dbal\DbalContext
     */
    protected $psrContext;

    /**
     * BeanstalkProducer constructor.
     *
     * @param \Enqueue\Dbal\DbalContext $context
     */
    public function __construct(DbalContext $context)
    {
        $this->psrContext = $context;
    }

    /**
     * @param string $body
     * @param string $queueName
     * @param int $delay - The delay in seconds
     *
     * @throws \Interop\Queue\Exception
     */
    public function produceMessage($body, $queueName, $delay = 0)
    {
        $queue = $this->psrContext->createQueue($queueName);
        $message = $this->psrContext->createMessage($body);
        $message->setDeliveryDelay($delay * 1000);

        $producer = $this->psrContext->createProducer();
        $producer->send($queue, $message);
    }

}