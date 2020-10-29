<?php

namespace Chamilo\Application\ExamAssignment\Ajax\Component;

use Chamilo\Application\ExamAssignment\Ajax\Manager;
use Chamilo\Application\ExamAssignment\Service\ExamUploaderService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Application\ExamAssignment\Component
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class UploadExamComponent extends Manager
{
    /**
     * @return JsonResponse
     */
    function run()
    {
        try
        {
            $publicationId = $this->getRequest()->getFromPost(self::PARAM_CONTENT_OBJECT_PUBLICATION_ID);
            $securityCode = $this->getRequest()->getFromPostOrUrl(self::PARAM_SECURITY_CODE);
            $uploadedFile = $this->getRequest()->files->get('file');
            if (empty($uploadedFile))
            {
                throw new \RuntimeException('Could not find the uploaded file');
            }

            if (substr($uploadedFile->getClientOriginalName(), 0,1) == 'i') {
                throw new \RuntimeException('File name cannot start with an F.');
            }

            $ipAddress = $this->getIpAddress();

            $this->getExamUploaderService()->uploadFileToAssignment(
                $this->getUser(), $publicationId, $uploadedFile, $securityCode, $ipAddress
            );

            return new JsonResponse([], 200);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            return new JsonResponse(['error' => $ex->getMessage()], 500);
        }
    }

    /**
     * @return string
     */
    protected function getIpAddress()
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
        {
            $possibleAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddress = array_pop($possibleAddresses);
        }

        return $ipAddress;
    }

    /**
     * @return ExamUploaderService
     */
    protected function getExamUploaderService()
    {
        return $this->getService(ExamUploaderService::class);
    }

}
