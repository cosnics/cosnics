<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\WebhookRequestData;
use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Application\Plagiarism\Service\StrikePlagiarism\WebhookHandler;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;

class StrikePlagiarismWebhookComponent extends Manager
{
    const SIGNATURE = 'signature';

    public function run()
    {
        try
        {
            $request = $this->getRequest();

            $requestBody = $request->getContent();
            $requestData = $this->getSymfonySerializer()->deserialize(
                $requestBody, WebhookRequestData::class,'json'
            );

            $this->getWebhookHandler()->handleWebhookRequest(
                $requestData, $this->getRequest()->getFromUrl(self::SIGNATURE)
            );
        }
        catch(\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            return new JsonResponse(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }

        return new JsonResponse(['status' => 'new', 'message' => 'success']);
    }

    protected function getWebhookHandler(): WebhookHandler
    {
        return $this->getService(WebhookHandler::class);
    }
}