<?php

namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
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

        $this->handleNoFileUploaded($file);

        $this->handleInvalidFile($file);

        $fileContentObject = $this->handleFileCreate($file);

        $thumbnailUrl = $this->getThumbnailUrl($fileContentObject);

        $result = array(
            "uploaded" => 1,
            "filename" => $fileContentObject->get_filename(),
            "co-id" => $fileContentObject->getId(),
            "security-code" => $fileContentObject->calculate_security_code(),
            "type" => 'image',
            "url" => $thumbnailUrl
        );

        $response = new JsonResponse($result);
        $response->send();

    }


    /**
     *
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile()
    {

        $file = $this->getRequest()->files->get('upload');
        if(empty($file)) {
            $errorMessage = "File with key upload not found in request.";

            throw new \Exception($errorMessage);
        }

        return $file;
    }

    /**
     * @param $file
     */
    protected function handleNoFileUploaded($file)
    {
        if (!$file) {
            $result = array(
                "uploaded" => 0,
                "error" => array(
                    "message" => Translation::getInstance()->getTranslation('NoFileUploaded', null, Utilities::COMMON_LIBRARIES)
                )
            );
            $response = new JsonResponse($result);
            $response->send();
        }
    }

    /**
     * @param $file
     */
    protected function handleInvalidFile($file)
    {
        if (!$file->isValid()) {
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

    /**
     * @param $file
     * @return File
     */
    protected function handleFileCreate($file)
    {
        $fileContentObject = new File();
        $title = substr($file->getClientOriginalName(), 0, -(strlen($file->getClientOriginalExtension()) + 1));

        $fileContentObject->set_title($title);
        $fileContentObject->set_description($file->getClientOriginalName());
        $fileContentObject->set_owner_id($this->getUser()->getId());
        $fileContentObject->set_parent_id(0);
        $fileContentObject->set_filename($file->getClientOriginalName());

        $fileContentObject->set_temporary_file_path($file->getRealPath());

        if (!$fileContentObject->create()) {
            $result = array(
                "uploaded" => 0,
                "error" => array(
                    "message" => Translation::getInstance()->getTranslation('FileUploadFailed', null, Utilities::COMMON_LIBRARIES)
                )
            );
            $response = new JsonResponse($result);
            $response->send();
        }

        return $fileContentObject;
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
}