<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Manager;
use Chamilo\Application\Plagiarism\Service\StrikePlagiarism\WebhookHandler;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use function _PHPStan_22f755c6a\RingCentral\Psr7\build_query;

class StrikePlagiarismWebhookComponent extends Manager implements NoAuthenticationSupport
{
    const SIGNATURE = 'signature';
    const PARAM_ID = 'id';

    public function run()
    {
        $file = Path::getInstance()->getLogPath(). 'webhook.log';
        $data = print_r($_POST, true);

        file_put_contents($file, $_SERVER['REQUEST_URI'] . "\n\n" . $data);

        try
        {
            $request = $this->getRequest();
            $documentId = $request->getFromPost(self::PARAM_ID);

            $this->getWebhookHandler()->handleWebhookRequest(
                $documentId, $this->getRequest()->getFromUrl(self::SIGNATURE)
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