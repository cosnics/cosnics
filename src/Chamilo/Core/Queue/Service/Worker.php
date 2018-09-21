<?php

namespace Chamilo\Core\Queue\Service;

use Interop\Queue\PsrContext;

/**
 * @package Chamilo\Core\Queue\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Worker
{
    /**
     * @var \Interop\Queue\PsrContext
     */
    protected $psrContext;

    /**
     * Worker constructor.
     *
     * @param \Interop\Queue\PsrContext $psrContext
     */
    public function __construct(PsrContext $psrContext)
    {
        $this->psrContext = $psrContext;
    }

    /**
     * @param string $topic
     */
    public function waitForJobAndExecute($topic)
    {
        $destination = $this->psrContext->createQueue($topic);
        $consumer = $this->psrContext->createConsumer($destination);
        $message = $consumer->receive();
        var_dump($message);
        $consumer->acknowledge($message);
    }
}