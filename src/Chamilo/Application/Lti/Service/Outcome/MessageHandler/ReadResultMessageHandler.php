<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ReadResultMessageHandler extends MessageHandler
{
    /**
     * Executes the correct method on the integration for the given message
     *
     * @param \Chamilo\Application\Lti\Service\Outcome\IntegrationInterface $integration
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     */
    protected function callIntegration(
        IntegrationInterface $integration, OutcomeMessage $outcomeMessage
    )
    {
        $integration->deleteResult($outcomeMessage->getId());
    }
}