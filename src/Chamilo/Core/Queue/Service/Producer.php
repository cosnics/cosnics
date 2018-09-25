<?php

namespace Chamilo\Core\Queue\Service;

use Chamilo\Core\Queue\Domain\Job;
use Interop\Queue\PsrContext;

class Producer
{
    /**
     * @var \Interop\Queue\PsrContext
     */
    protected $psrContext;

    /**
     * @var JobSerializer
     */
    protected $jobSerializer;

    /**
     * Producer constructor.
     *
     * @param \Interop\Queue\PsrContext $psrContext
     * @param JobSerializer $jobSerializer
     */
    public function __construct(PsrContext $psrContext, JobSerializer $jobSerializer)
    {
        $this->psrContext = $psrContext;
        $this->jobSerializer = $jobSerializer;
    }

    /**
     * @param \Chamilo\Core\Queue\Domain\Job $job
     * @param string $queueName
     *
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\InvalidDestinationException
     * @throws \Interop\Queue\InvalidMessageException
     */
    public function sendJob(Job $job, string $queueName)
    {
        $messageContent = $this->jobSerializer->serializeJob($job);

        $queue = $this->psrContext->createQueue($queueName);
        $message = $this->psrContext->createMessage($messageContent);

        $producer = $this->psrContext->createProducer();
        $producer->send($queue, $message);
    }

}