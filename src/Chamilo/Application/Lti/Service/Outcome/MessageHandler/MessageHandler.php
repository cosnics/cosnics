<?php

namespace Chamilo\Application\Lti\Service\Outcome\MessageHandler;

use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;
use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;
use Chamilo\Application\Lti\Service\Integration\TestIntegration;
use Chamilo\Application\Lti\Service\Outcome\IntegrationLocator;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
abstract class MessageHandler
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     * @var \Chamilo\Application\Lti\Service\Outcome\IntegrationLocator
     */
    protected $integrationLocator;

    /**
     * MessageHandler constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     * @param \Chamilo\Application\Lti\Service\Outcome\IntegrationLocator $integrationLocator
     */
    public function __construct(
        \Twig_Environment $twig, ExceptionLoggerInterface $exceptionLogger, IntegrationLocator $integrationLocator
    )
    {
        $this->twig = $twig;
        $this->exceptionLogger = $exceptionLogger;
        $this->integrationLocator = $integrationLocator;
    }

    /**
     * Handles a message and returns a response
     *
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     *
     * @return string
     */
    public function handleMessage(OutcomeMessage $outcomeMessage)
    {
        $integration = $this->integrationLocator->locateIntegration($outcomeMessage);
        return $this->callIntegration($integration, $outcomeMessage);
    }

    /**
     * Executes the correct method on the integration for the given message
     *
     * @param \Chamilo\Application\Lti\Service\Integration\IntegrationInterface $integration
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     *
     * @return string
     */
    abstract protected function callIntegration(
        IntegrationInterface $integration, OutcomeMessage $outcomeMessage
    );

}