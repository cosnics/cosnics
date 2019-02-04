<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Application\Plagiarism\Service\Turnitin\WebhookHandler;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinWebhookComponent extends Manager implements NoAuthenticationSupport
{

    /**
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    function run()
    {
        try
        {
            $request = $this->getRequest();

            $eventType = $request->headers->get('X-Turnitin-EventType');
            $authorizationKey = $request->headers->get('X-Turnitin-Signature');

            $requestBody = $request->getContent();

            $this->getWebhookHandler()->handleWebhookRequest($eventType, $authorizationKey, $requestBody);
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            return new JsonResponse(['error' => $ex->getMessage()], 500);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\Turnitin\WebhookHandler
     */
    protected function getWebhookHandler()
    {
        return $this->getService(WebhookHandler::class);
    }
}