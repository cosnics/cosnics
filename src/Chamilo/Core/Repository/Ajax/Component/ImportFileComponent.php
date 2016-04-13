<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

class ImportFileComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_PARENT_ID = 'parentId';
    const PROPERTY_CONTENT_OBJECT_ID = 'contentObjectId';
    const PROPERTY_VIEW_BUTTON = 'viewButton';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_PARENT_ID);
    }

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
        $document->set_parent_id($this->getPostDataValue(self :: PARAM_PARENT_ID));
        $document->set_filename($file->getClientOriginalName());

        // $hash = md5_file($file->getRealPath());

        $document->set_temporary_file_path($file->getRealPath());

        if ($document->create())
        {
            $viewLink = new Redirect(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(),
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_VIEW_CONTENT_OBJECTS,
                    \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID => $document->get_id(),
                    FilterData :: FILTER_CATEGORY => $document->get_parent_id()));

            $viewButton = array();
            $viewButton[] = '<a class="btn btn-primary view" target="_blank" href="' . $viewLink->getUrl() . '">';
            $viewButton[] = '<i class="glyphicon glyphicon-file"></i> <span>' . Translation :: get('View') . '</span>';
            $viewButton[] = '</a>';

            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_properties(
                array(
                    self :: PROPERTY_CONTENT_OBJECT_ID => $document->getId(),
                    self :: PROPERTY_VIEW_BUTTON => implode(PHP_EOL, $viewButton)));
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