<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;

class DeleteFileComponent extends Manager
{
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_CONTENT_OBJECT_ID);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $contentObjectId = $this->getPostDataValue(self::PARAM_CONTENT_OBJECT_ID);
        
        if (isset($contentObjectId))
        {
            $conditions = [];
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
                new StaticConditionVariable(File::class));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
                new StaticConditionVariable($this->getUser()->getId()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                new StaticConditionVariable($contentObjectId));
            
            $file = DataManager::retrieve(
                ContentObject::class,
                new DataClassRetrieveParameters(new AndCondition($conditions)));
            
            if ($file instanceof File)
            {
                if (DataManager::content_object_deletion_allowed($file, 'version'))
                {
                    if (! $file->delete(true))
                    {
                        JsonAjaxResult::general_error(Translation::get('FileNotDeleted'));
                    }
                    else
                    {
                        $jsonAjaxResult = new JsonAjaxResult();
                        $jsonAjaxResult->display();
                    }
                }
                else
                {
                    JsonAjaxResult::not_allowed();
                }
            }
            else
            {
                JsonAjaxResult::general_error(Translation::get('NoFileSelected'));
            }
        }
        else
        {
            JsonAjaxResult::general_error(Translation::get('NoFileSelected'));
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