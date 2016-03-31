<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class ImportFileComponent extends \Chamilo\Core\Repository\Ajax\Manager implements NoAuthenticationSupport
{

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $file = $this->getFile();
        $document = new File();

        $document->set_title($file->getClientOriginalName());
        $document->set_description($file->getClientOriginalName());
        $document->set_owner_id($this->get_user_id());
        $document->set_parent_id(0);
        $document->set_filename($file->getClientOriginalName());

        // $hash = md5_file($file->getRealPath());

        $document->set_temporary_file_path($file->getRealPath());

        if ($document->create())
        {
            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_properties(array('contentObjectId' => $document->getId()));
            $jsonAjaxResult->display();
        }
        else
        {
            JsonAjaxResult :: general_error(Translation :: get('ObjectNotImported'));
        }
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile()
    {
        $filePropertyName = $this->getRequest()->request->get('filePropertyName');
        return $this->getRequest()->files->get($filePropertyName);
    }
}