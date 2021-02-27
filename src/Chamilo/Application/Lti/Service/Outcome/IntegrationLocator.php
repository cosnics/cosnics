<?php

namespace Chamilo\Application\Lti\Service\Outcome;

use Chamilo\Application\Lti\Domain\Exception\LTIException;
use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;
use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;
use Chamilo\Application\Lti\Service\Integration\TestIntegration;

/**
 * Locates an integration based on the value of an outcome message
 *
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class IntegrationLocator
{
    /**
     * @var \Chamilo\Application\Lti\Service\Integration\IntegrationInterface[]
     */
    protected $integrations;

    /**
     * IntegrationLocator constructor.
     */
    public function __construct()
    {
        $this->integrations = [];
    }

    /**
     * @param \Chamilo\Application\Lti\Service\Integration\IntegrationInterface $integration
     */
    public function addIntegration(IntegrationInterface $integration)
    {
        $this->integrations[get_class($integration)] = $integration;
    }

    /**
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     *
     * @return \Chamilo\Application\Lti\Service\Integration\IntegrationInterface
     */
    public function locateIntegration(OutcomeMessage $outcomeMessage)
    {
        if (!array_key_exists($outcomeMessage->getIntegrationClass(), $this->integrations))
        {
            throw new LTIException(
                sprintf('Could not locate the integration class %s', $outcomeMessage->getIntegrationClass())
            );
        }

        return $this->integrations[$outcomeMessage->getIntegrationClass()];
    }
}