<?php

namespace Chamilo\Core\Queue\Service\Producer;

/**
 * Interface ProducerInterface
 *
 * @package Chamilo\Core\Queue\Service\Producer
 */
interface ProducerInterface
{
    /**
     * @param string $body
     * @param string $queueName
     * @param int $delay - The delay in seconds
     */
    public function produceMessage($body, $queueName, $delay = 0);
}