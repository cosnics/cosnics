<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\UUID;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UploadTemporaryFileComponent extends Manager
{

    /**
     * @throws \Exception
     */
    public function run()
    {
        $file = $this->getFile();

        if (!$file->isValid())
        {
            JsonAjaxResult::bad_request(
                $this->getTranslator()->trans('NoValidFileUploaded', array(), Utilities::COMMON_LIBRARIES)
            );
        }
        $temporaryPath = $this->getConfigurablePathBuilder()->getTemporaryPath(__NAMESPACE__);

        Filesystem::create_dir($temporaryPath);

        $fileName = md5(UUID::v4());
        $temporaryFilePath = $temporaryPath . $fileName;

        $result = move_uploaded_file($file->getRealPath(), $temporaryFilePath);

        if (!$result)
        {
            JsonAjaxResult::general_error(
                $this->getTranslator()->trans('FileNotUploaded', array(), Utilities::COMMON_LIBRARIES)
            );
        }
        else
        {
            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_properties(array('temporaryFileName' => $fileName));
            $jsonAjaxResult->display();
        }
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     * @throws \Exception
     */
    public function getFile()
    {
        $filePropertyName = $this->getRequest()->request->get('filePropertyName');
        if (empty($filePropertyName))
        {
            throw new Exception('filePropertyName parameter not available in request');
        }

        $file = $this->getRequest()->files->get($filePropertyName);
        if (empty($file))
        {
            $errorMessage = "File with key " . $filePropertyName . "not found in request.";

            $availableKeys = $this->getRequest()->files->keys();
            if (!empty($availableKeys))
            {
                $errorMessage .= " Available file keys: " . implode(', ', $availableKeys) . ".";
            }

            throw new Exception($errorMessage);
        }

        return $file;
    }
}