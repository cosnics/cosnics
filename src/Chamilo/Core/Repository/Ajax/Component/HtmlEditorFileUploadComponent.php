<?php

namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\DTO\HtmlEditorContentObjectPlaceholder;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class HtmlEditorFileUploadComponent
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class HtmlEditorFileUploadComponent extends Manager
{

    function run()
    {
        $file = $this->getFile();

        if(!$file) {
            $this->handleNoFileUploaded();
            return;
        }

        if(!$file->isValid()) {
            $this->handleInvalidFile($file);
            return;
        }

        try {
            $contentObjectPlaceholder = $this->handleUploadedFile($file);
        } catch (\Exception $exception) {
            $this->handleFileCreationFailed($exception);
            return;
        }

        $this->handleContentObjectCreationSuccess($contentObjectPlaceholder);

    }


    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->getRequest()->files->get('upload');
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return HtmlEditorContentObjectPlaceholder
     * @throws \Exception
     */
    protected function handleUploadedFile(UploadedFile $uploadedFile)
    {
        $registrations = $this->getRegistrationConsulter()->getIntegrationRegistrations('Chamilo\Core\Repository');

        usort($registrations, function($registrationA, $registrationB) {
            return $registrationA[Registration::PROPERTY_PRIORITY] > $registrationB[Registration::PROPERTY_PRIORITY];
        });

        $uploadedFileHandler = null;
        foreach ($registrations as $registration)
        {
            if (class_exists($registration[Registration::PROPERTY_CONTEXT] . '\HtmlEditorUploadedFileHandler'))
            {
                $className = $registration[Registration::PROPERTY_CONTEXT] . '\HtmlEditorUploadedFileHandler';
                $uploadedFileHandlerCandidate = new $className;
                if ($uploadedFileHandlerCandidate->canHandleUploadedFile($uploadedFile))
                {
                    $uploadedFileHandler = $uploadedFileHandlerCandidate;
                }
            }
        }

        if (empty($uploadedFileHandler))
        {
            throw new \Exception('No Handler defined for uploaded file: ' . $uploadedFile->getFilename());
        }

        return $uploadedFileHandler->handle($uploadedFile, $this->getUser());
    }

    /**
     * @param $fileContentObject
     * @return string
     */
    protected function getThumbnailUrl($fileContentObject)
    {
        try {
            $display = ContentObjectRenditionImplementation:: factory(
                $fileContentObject,
                'json',
                'image',
                $this
            );

            $rendition = $display->render();
        } catch (\Exception $ex) {
            $rendition = array('url' => Theme::getInstance()->getCommonImagePath('NoThumbnail'));
        }

        return $rendition['url'];
    }

    /**
     * @return RegistrationConsulter
     */
    protected function getRegistrationConsulter()
    {
        return $this->getContainer()->get("chamilo.configuration.service.registration_consulter");
    }

    /**
     * @param \Exception $exception
     */
    protected function handleFileCreationFailed(\Exception $exception)
    {
        $this->getExceptionLogger()->logException($exception);

        $result = array(
            "uploaded" => 0,
            "error" => array(
                "message" => Translation::getInstance()->getTranslation('FileCreationFailed', null, Utilities::COMMON_LIBRARIES)
            )
        );

        $response = new JsonResponse($result);
        $response->send();
    }

    /**
     * @param HtmlEditorContentObjectPlaceholder $placeholder
     */
    protected function handleContentObjectCreationSuccess(HtmlEditorContentObjectPlaceholder $placeholder)
    {
        $result = $placeholder->asArray();
        $result["uploaded"] = 1;

        $response = new JsonResponse($result);
        $response->send();
    }

    /**
     *
     */
    protected function handleNoFileUploaded()
    {
        $result = array(
            "uploaded" => 0,
            "error" => array(
                "message" => Translation::getInstance()->getTranslation('NoFileUploaded', null, Utilities::COMMON_LIBRARIES)
            )
        );
        $response = new JsonResponse($result);
        $response->send();
    }

    /**
     * @param UploadedFile $file
     */
    protected function handleInvalidFile(UploadedFile $file)
    {
        $result = array(
            "uploaded" => 0,
            "error" => array(
                "message" => Translation::getInstance()->getTranslation('NoValidFileUploaded', null, Utilities::COMMON_LIBRARIES)
            )
        );
        $response = new JsonResponse($result);
        $response->send();
    }
}