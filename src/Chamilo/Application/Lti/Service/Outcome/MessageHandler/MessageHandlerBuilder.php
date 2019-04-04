<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Domain\Exception\UnsupportedActionException;
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
     * Defines a message handler for a given action
     *
     * @param string $action
     * @param \Chamilo\Application\Lti\Service\Outcome\MessageHandler $messageHandler
     */
    public function addMessageHandler(string $action, MessageHandler $messageHandler)
    {
        $this->messageHandlers[$action] = $messageHandler;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $message
     *
     * @return \Chamilo\Application\Lti\Service\Outcome\MessageHandler
     */
    public function buildMessageHandler(OutcomeMessage $message)
    {
        if(!array_key_exists($message->getAction(), $this->messageHandlers))
        {
            throw new UnsupportedActionException($message->getAction());
        }

        return $this->messageHandlers[$message->getAction()];
    }
}