<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
abstract class MessageHandler
{
    /**
     * @var \Chamilo\Application\Lti\Service\Outcome\IntegrationInterface[]
     */
    protected $integrations;

    /**
     * @param \Chamilo\Application\Lti\Service\Outcome\IntegrationInterface $integration
     */
    public function addIntegration(IntegrationInterface $integration)
    {
        $this->integrations[] = $integration;
    }

    /**
     * Handles an outcome message
     *
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     */
    public function handleMessage(OutcomeMessage $outcomeMessage)
    {
        foreach ($this->integrations as $integration)
        {
            $this->callIntegration($integration, $outcomeMessage);
        }
    }

    /**
     * Executes the correct method on the integration for the given message
     *
     * @param \Chamilo\Application\Lti\Service\Outcome\IntegrationInterface $integration
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     */
    abstract protected function callIntegration(
        IntegrationInterface $integration, OutcomeMessage $outcomeMessage
    );

}