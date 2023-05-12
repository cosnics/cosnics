<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class ImportFileComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    public const PARAM_PARENT_ID = 'parentId';
    public const PARAM_WORKSPACE_ID = 'workspaceId';

    public const PROPERTY_CONTENT_OBJECT_ID = 'contentObjectId';
    public const PROPERTY_CONTENT_OBJECT_TITLE = 'contentObjectTitle';
    public const PROPERTY_UPLOADED_MESSAGE = 'uploadedMessage';
    public const PROPERTY_VIEW_BUTTON = 'viewButton';

    public function run()
    {
        $file = $this->getFile();

        if (!$file)
        {
            JsonAjaxResult::bad_request(
                Translation::getInstance()->getTranslation('NoFileUploaded', null, StringUtilities::LIBRARIES)
            );
        }

        if (!$file->isValid())
        {
            JsonAjaxResult::bad_request(
                Translation::getInstance()->getTranslation('NoValidFileUploaded', null, StringUtilities::LIBRARIES)
            );
        }

        $document = new File();

        $categoryId = $this->getPostDataValue(self::PARAM_PARENT_ID);

        $workspaceId = $this->getRequest()->getFromRequest(self::PARAM_WORKSPACE_ID);
        $workspace = $this->getWorkspaceService()->getWorkspaceByIdentifier($workspaceId);

        if (!$workspace instanceof Workspace)
        {
            $document->set_parent_id($categoryId);
        }

        $title = substr($file->getClientOriginalName(), 0, - (strlen($file->getClientOriginalExtension()) + 1));

        $document->set_title($title);
        $document->set_description($file->getClientOriginalName());
        $document->set_owner_id($this->get_user_id());

        $document->set_filename($file->getClientOriginalName());

        // $hash = md5_file($file->getRealPath());

        $document->set_temporary_file_path($file->getRealPath());

        if ($document->create())
        {
            if ($workspace instanceof Workspace)
            {
                $this->getContentObjectRelationService()->createContentObjectRelationFromParameters(
                    $workspace->getId(), $document->get_object_number(), $categoryId
                );
            }

            $previewLink = \Chamilo\Core\Repository\Preview\Manager::get_content_object_default_action_link($document);
            $onclick = 'onclick="javascript:openPopup(\'' . addslashes($previewLink) . '\'); return false;';

            $viewButton = [];
            $viewButton[] = '<a class="btn btn-primary view" target="_blank" ' . $onclick . ' ">';

            $glyph = new FontAwesomeGlyph('desktop', [], null, 'fas');

            $viewButton[] = $glyph->render() . ' <span>';

            $viewButton[] = Translation::getInstance()->getTranslation(
                'ViewImportedObject', null, Manager::CONTEXT
            );

            $viewButton[] = '</span>';
            $viewButton[] = '</a>';

            $uploadedMessage = [];
            $uploadedMessage[] = '<div class="alert alert-success alert-import-success">';
            $uploadedMessage[] = Translation::getInstance()->getTranslation(
                'FileImported', ['CATEGORY' => $this->getCategoryTitle($categoryId)], Manager::CONTEXT
            );
            $uploadedMessage[] = '</div>';

            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_properties(
                [
                    self::PROPERTY_CONTENT_OBJECT_ID => $document->getId(),
                    self::PROPERTY_VIEW_BUTTON => implode(PHP_EOL, $viewButton),
                    self::PROPERTY_UPLOADED_MESSAGE => implode(PHP_EOL, $uploadedMessage),
                    self::PROPERTY_CONTENT_OBJECT_TITLE => $title
                ]
            );
            $jsonAjaxResult->display();
        }
        else
        {
            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_result_code(500);
            $jsonAjaxResult->set_result_message(Translation::get('ObjectNotImported'));
            $jsonAjaxResult->set_properties(['object' => serialize($document)]);
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
            return Translation::getInstance()->getTranslation('MyRepository', null, Manager::CONTEXT);
        }

        $category = DataManager::retrieve_by_id(
            RepositoryCategory::class, $categoryId
        );

        if (!$category)
        {
            return null;
        }

        return $category->get_name();
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */

    protected function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->getService(ContentObjectRelationService::class);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile()
    {
        $filePropertyName = $this->getRequest()->request->get('filePropertyName');

        return $this->getRequest()->files->get($filePropertyName);
    }

    public function getRequiredPostParameters(): array
    {
        return [self::PARAM_PARENT_ID];
    }
}
