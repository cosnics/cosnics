<?php

namespace Chamilo\Application\Lti\Service\Outcome\MessageHandler;

use Chamilo\Application\Lti\Domain\Exception\UnsupportedOperationException;
use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class MessageHandlerBuilder
{
    /**
     * @var MessageHandler[]
     */
    protected $messageHandlers;

    /**
     * Defines a message handler for a given operation
     *
     * @param string $operation
     * @param \Chamilo\Application\Lti\Service\Outcome\MessageHandler\MessageHandler $messageHandler
     */
    public function addMessageHandler(string $operation, MessageHandler $messageHandler)
    {
        $this->messageHandlers[$operation] = $messageHandler;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $message
     *
     * @return \Chamilo\Application\Lti\Service\Outcome\MessageHandler\MessageHandler
     */
    public function buildMessageHandler(OutcomeMessage $message)
    {
        if(!array_key_exists($message->getOperation(), $this->messageHandlers))
        {
            throw new UnsupportedOperationException($message->getOperation());
        }

        return $this->messageHandlers[$message->getOperation()];
    }
}