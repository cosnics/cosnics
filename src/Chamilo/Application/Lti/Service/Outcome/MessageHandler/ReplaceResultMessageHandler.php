<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Domain\Exception\LTIException;
use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ReplaceResultMessageHandler extends MessageHandler
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
        if (!$outcomeMessage->isValidScore())
        {
            throw new LTIException(
                sprintf('The given score %s is invalid and should be between 0.0 and 1.0', $outcomeMessage->getScore())
            );
        }

        $integration->replaceResult($outcomeMessage->getId(), $outcomeMessage->getScore());
    }
}