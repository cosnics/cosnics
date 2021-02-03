<?php

namespace Chamilo\Application\Lti\Service\Outcome\MessageHandler;

use Chamilo\Application\Lti\Domain\Exception\LTIException;
use Chamilo\Application\Lti\Domain\Outcome\OutcomeMessage;
use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;

/**
 * @package Chamilo\Application\Lti\Service\Outcome
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ReplaceResultMessageHandler extends MessageHandler
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
        try
        {
            if (!$outcomeMessage->isValidScore())
            {
                throw new LTIException(
                    sprintf(
                        'The given score %s is invalid and should be between 0.0 and 1.0', $outcomeMessage->getScore()
                    )
                );
            }

            $integration->replaceResult($outcomeMessage->getResultId(), $outcomeMessage->getScore());

            $success = true;
            $message = sprintf(
                'The result for id %s was successfully replaced with %s', $outcomeMessage->getResultId(),
                $outcomeMessage->getScore()
            );
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = sprintf(
                'The result for id %s could not be replaced. REASON: %s', $outcomeMessage->getResultId(),
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