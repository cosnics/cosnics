<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ImportFileComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_PARENT_ID = 'parentId';
    const PROPERTY_CONTENT_OBJECT_ID = 'contentObjectId';
    const PROPERTY_VIEW_BUTTON = 'viewButton';
    const PROPERTY_UPLOADED_MESSAGE = 'uploadedMessage';
    const PROPERTY_CONTENT_OBJECT_TITLE = 'contentObjectTitle';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_PARENT_ID);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $file = $this->getFile();

        if (!$file)
        {
            JsonAjaxResult::bad_request(
                Translation::getInstance()->getTranslation('NoFileUploaded', null, Utilities::COMMON_LIBRARIES)
            );
        }

        if (!$file->isValid())
        {
            JsonAjaxResult::bad_request(
                Translation::getInstance()->getTranslation('NoValidFileUploaded', null, Utilities::COMMON_LIBRARIES)
            );
        }

        $document = new File();

        $categoryId = $this->getPostDataValue(self::PARAM_PARENT_ID);
        $title = substr($file->getClientOriginalName(), 0, - (strlen($file->getClientOriginalExtension()) + 1));

        $document->set_title($title);
        $document->set_description($file->getClientOriginalName());
        $document->set_owner_id($this->get_user_id());
        $document->set_parent_id($categoryId);
        $document->set_filename($file->getClientOriginalName());

        // $hash = md5_file($file->getRealPath());

        $document->set_temporary_file_path($file->getRealPath());

        if ($document->create())
        {
            $previewLink = \Chamilo\Core\Repository\Preview\Manager::get_content_object_default_action_link($document);
            $onclick = 'onclick="javascript:openPopup(\'' . $previewLink . '\'); return false;';

            $viewButton = array();
            $viewButton[] = '<a class="btn btn-primary view" target="_blank" ' . $onclick . ' ">';
            $viewButton[] = '<i class="glyphicon glyphicon-file"></i> <span>';

            $viewButton[] = Translation::getInstance()->getTranslation(
                'ViewImportedObject',
                null,
                \Chamilo\Core\Repository\Manager::context()
            );

            $viewButton[] = '</span>';
            $viewButton[] = '</a>';

            $uploadedMessage = array();
            $uploadedMessage[] = '<div class="alert alert-success alert-import-success">';
            $uploadedMessage[] = Translation::getInstance()->getTranslation(
                'FileImported',
                array('CATEGORY' => $this->getCategoryTitle($categoryId)),
                \Chamilo\Core\Repository\Manager::context()
            );
            $uploadedMessage[] = '</div>';

            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_properties(
                array(
                    self::PROPERTY_CONTENT_OBJECT_ID => $document->getId(),
                    self::PROPERTY_VIEW_BUTTON => implode(PHP_EOL, $viewButton),
                    self::PROPERTY_UPLOADED_MESSAGE => implode(PHP_EOL, $uploadedMessage),
                    self::PROPERTY_CONTENT_OBJECT_TITLE => $title
                )
            );
            $jsonAjaxResult->display();
        }
        else
        {
            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_result_code(500);
            $jsonAjaxResult->set_result_message(Translation::get('ObjectNotImported'));
            $jsonAjaxResult->set_properties(array('object' => serialize($document)));
            $jsonAjaxResult->display();
        }
    }

    /**
     * Returns the title of a given category
     *
     * @param $categoryId
     *
     * @return string
     */
    protected function getCategoryTitle($categoryId)
    {
        if (!$categoryId)
        {
            return Translation::getInstance()->getTranslation('MyRepository', null, Manager::context());
        }

        $category = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            RepositoryCategory::class_name(),
            $categoryId
        );

        if (!$category)
        {
            return null;
        }

        return $category->get_name();
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
