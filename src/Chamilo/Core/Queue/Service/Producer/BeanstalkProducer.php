<?php

namespace Chamilo\Core\Queue\Service\Producer;

use Enqueue\Pheanstalk\PheanstalkContext;

/**
 * @package Chamilo\Core\Queue\Service\Producer
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BeanstalkProducer implements ProducerInterface
{
    /**
     * @var \Enqueue\Pheanstalk\PheanstalkContext
     */
    protected $psrContext;

    /**
     * BeanstalkProducer constructor.
     *
     * @param \Enqueue\Pheanstalk\PheanstalkContext $context
     */
    public function __construct(PheanstalkContext $context)
    {
        $this->psrContext = $context;
    }

    /**
     * @param string $body
     * @param string $queueName
     * @param int $delay - The delay in seconds
     */
    public function produceMessage($body, $queueName, $delay = 0)
    {
        $queue = $this->psrContext->createQueue($queueName);
        $message = $this->psrContext->createMessage($body);
        $message->setDelay($delay);

        $producer = $this->psrContext->createProducer();
        $producer->send($queue, $message);
    }

}