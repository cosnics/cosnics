<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\UUID;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UploadTemporaryFileComponent extends \Chamilo\Libraries\Ajax\Manager
{

    public function run()
    {
        $file = $this->getFile();

        $temporaryPath = Path :: getInstance()->getTemporaryPath(__NAMESPACE__);
        $owner = $this->getPostDataValue(\Chamilo\Core\User\Manager :: PARAM_USER_USER_ID);

        Filesystem :: create_dir($temporaryPath);

        $fileName = md5(UUID :: v4());
        $temporaryFilePath = $temporaryPath . $fileName;

        $result = move_uploaded_file($file->getRealPath(), $temporaryFilePath);

        if (! $result)
        {
            JsonAjaxResult :: general_error(Translation :: get('FileNotUploaded'));
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
     */
    public function getFile()
    {
        return $this->getRequest()->files->get('file');
    }
}