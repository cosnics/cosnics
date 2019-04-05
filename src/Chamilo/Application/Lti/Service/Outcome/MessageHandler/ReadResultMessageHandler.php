<?php

namespace Chamilo\Application\Lti\Service\Outcome\MessageHandler;

use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;
use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ReadResultMessageHandler extends MessageHandler
{
    /**
     * Executes the correct method on the integration for the given message
     *
     * @param \Chamilo\Application\Lti\Service\Integration\IntegrationInterface $integration
     * @param \Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage $outcomeMessage
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function callIntegration(
        IntegrationInterface $integration, OutcomeMessage $outcomeMessage
    )
    {
        $score = null;

        try
        {
            $score = $integration->readResult($outcomeMessage->getResultId());

            $success = true;
            $message = sprintf('The score for result with id %s is %s', $outcomeMessage->getResultId(), $score);
        }
        catch (\Exception $ex)
        {
            $success = false;

            $message = sprintf(
                'The score for the result with id %s could not be retrieved. REASON: %s',
                $outcomeMessage->getResultId(), $ex->getMessage()
            );

            $this->exceptionLogger->logException($ex);
        }

        $parameters = $outcomeMessage->getResponseParametersArray($success, $message);
        $parameters['SCORE'] = $score;

        return $this->twig->render(
            'Chamilo\Application\Lti:Message/Outcome/ReadResultResponse.xml.twig', $parameters

        );
    }
}