<?php

namespace Chamilo\Application\Lti\Service\Outcome\MessageHandler;

use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;
use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class DeleteResultMessageHandler extends MessageHandler
{

    /**
     * Executes the correct method on the integration for the given message
     *
     * @param \Chamilo\Application\Lti\Service\Integration\IntegrationInterface $integration
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function callIntegration(
        IntegrationInterface $integration, OutcomeMessage $outcomeMessage
    )
    {
        try
        {
            $integration->deleteResult($outcomeMessage->getResultId());
            $success = true;
            $message = sprintf('The result with id %s was successfully deleted', $outcomeMessage->getResultId());
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = sprintf(
                'The result with id %s could not be deleted. REASON: %s', $outcomeMessage->getResultId(),
                $ex->getMessage()
            );

            $this->exceptionLogger->logException($ex);
        }

        return $this->twig->render(
            'Chamilo\Application\Lti:Message/DefaultResponse.xml.twig',
            $outcomeMessage->getResponseParametersArray($success, $message)
        );
    }
}