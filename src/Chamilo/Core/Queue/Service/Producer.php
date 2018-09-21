<?php

namespace Chamilo\Core\Queue\Service;

use Enqueue\Client\Message;

class Producer
{
    /**
     * @var \Interop\Queue\PsrContext
     */
    protected $psrContext;

    /**
     * Producer constructor.
     *
     * @param \Interop\Queue\PsrContext $psrContext
     */
    public function __construct(\Interop\Queue\PsrContext $psrContext)
    {
        $this->psrContext = $psrContext;
    }

    /**
     * @param string $queueName
     * @param string $messageContent
     *
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\InvalidDestinationException
     * @throws \Interop\Queue\InvalidMessageException
     */
    public function sendMessage(string $queueName, string $messageContent)
    {
        $queue = $this->psrContext->createQueue($queueName);
        $message = $this->psrContext->createMessage($messageContent);
        $producer = $this->psrContext->createProducer();
        $producer->send($queue, $message);
    }

}