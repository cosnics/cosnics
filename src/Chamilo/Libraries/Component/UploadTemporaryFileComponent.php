<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Protocol\Ajax\Architecture\Domain\JsonAjaxResult;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Libraries\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class UploadTemporaryFileComponent extends Manager
{
    /**
     * @throws \Exception
     */
    public function run(?User $currentUser = null): Response
    {
        $file = $this->getFile();

        if (!$file->isValid()) {
            return JsonAjaxResult::badRequest(
                $this->getTranslator()->trans('NoValidFileUploaded', [], StringUtilities::LIBRARIES)
            );
        }

        $temporaryPath = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__);

        $this->getFilesystem()->mkdir($temporaryPath);

        $fileName = md5(Uuid::v7()->__toString());
        $temporaryFilePath = $temporaryPath . $fileName;

        $result = move_uploaded_file($file->getRealPath(), $temporaryFilePath);

        if (!$result) {
            return JsonAjaxResult::generalError(
                $this->getTranslator()->trans('FileNotUploaded', [], StringUtilities::LIBRARIES)
            );
        }
        else {
            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->setProperties(['temporaryFileName' => $fileName]);

            return $jsonAjaxResult->getResponse();
        }
    }

    /**
     * @throws \Exception
     */
    public function getFile(): UploadedFile
    {
        $filePropertyName = $this->getRequest()->request->get('filePropertyName');
        if (empty($filePropertyName)) {
            throw new Exception('filePropertyName parameter not available in request');
        }

        $file = $this->getRequest()->files->get($filePropertyName);
        if (empty($file)) {
            $errorMessage = 'File with key ' . $filePropertyName . 'not found in request.';

            $availableKeys = $this->getRequest()->files->keys();
            if (!empty($availableKeys)) {
                $errorMessage .= ' Available file keys: ' . implode(', ', $availableKeys) . '.';
            }

            throw new Exception($errorMessage);
        }

        return $file;
    }
}