<?php
namespace Chamilo\Core\Repository\Common\Import\Webpage;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\File\FileContentObjectImportForm;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class WebpageContentObjectImportController extends ContentObjectImportController
{
    const FORMAT = 'Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage';

    public function run()
    {
        if (self::is_available())
        {
            if ($this->get_parameters()->get_document_type() == FileContentObjectImportForm::DOCUMENT_UPLOAD)
            {
                $file = $this->get_parameters()->get_file();
                $calculator = new Calculator(
                    \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                        \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                        (int) $this->get_parameters()->get_user()));
                
                if (! $calculator->canUpload($file->get_size()))
                {
                    $this->add_message(Translation::get('InsufficientDiskQuota'), self::TYPE_ERROR);
                    return array();
                }
            }
            else
            {
                $file = FileProperties::from_url($this->get_parameters()->get_link());
                
                if ($file->get_path() && $file->get_name() && $file->get_extension() && (in_array(
                    $file->get_extension(), 
                    array('html', 'htm')) || strpos($file->get_type(), 'text/html') !== false))
                {
                    $calculator = new Calculator(
                        \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                            \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                            (int) $this->get_parameters()->get_user()));
                    
                    if ($calculator->canUpload($file->get_size()))
                    {
                        $temp_path = Path::getInstance()->getTemporaryPath() . 'repository/import/webpage/' .
                             $file->get_name_extension();
                        
                        if (file_exists($temp_path))
                        {
                            $this->add_message(Translation::get('ObjectNotImported'), self::TYPE_ERROR);
                            return array();
                        }
                        else
                        {
                            $destination_dir = dirname($temp_path);
                            if (Filesystem::create_dir($destination_dir))
                            {
                                if (copy($file->get_path(), $temp_path))
                                {
                                    $file = FileProperties::from_path($temp_path);
                                }
                                else
                                {
                                    $this->add_message(Translation::get('ObjectNotImported'), self::TYPE_ERROR);
                                    return array();
                                }
                            }
                        }
                    }
                    else
                    {
                        $this->add_message(Translation::get('InsufficientDiskQuota'), self::TYPE_ERROR);
                        return array();
                    }
                }
                else
                {
                    $this->add_message(Translation::get('InvalidWebpageLink'), self::TYPE_ERROR);
                    return array();
                }
            }
            
            $document = new Webpage();
            $document->set_title($file->get_name());
            $document->set_description($file->get_name());
            $document->set_owner_id($this->get_parameters()->get_user());
            $document->set_parent_id($this->determine_parent_id());
            $document->set_filename($file->get_name_extension());
            
            $hash = md5_file($file->get_path());
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID), 
                new StaticConditionVariable($this->get_parameters()->get_user()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Webpage::class_name(), Webpage::PROPERTY_HASH), 
                new StaticConditionVariable($hash));
            $condition = new AndCondition($conditions);
            $content_objects = DataManager::retrieve_active_content_objects(Webpage::class_name(), $condition);
            
            if ($content_objects->size() > 0)
            {
                if ($content_objects->size() == 1)
                {
                    $content_object = $content_objects->next_result();
                    
                    $redirect = new Redirect(
                        array(
                            Application::PARAM_CONTEXT => Manager::package(), 
                            Application::PARAM_ACTION => Manager::ACTION_VIEW_CONTENT_OBJECTS, 
                            Manager::PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
                    
                    $this->add_message(
                        Translation::get('ObjectAlreadyExists', array('LINK' => $redirect->getUrl())), 
                        self::TYPE_ERROR);
                }
                else
                {
                    $this->add_message(Translation::get('ObjectAlreadyExistsMultipleTimes'), self::TYPE_ERROR);
                }
            }
            else
            {
                $document->set_temporary_file_path($file->get_path());
                
                if ($document->create())
                {
                    $this->process_workspace($document);
                    
                    $this->add_message(Translation::get('ObjectImported'), self::TYPE_CONFIRM);
                    return array($document->get_id());
                }
                else
                {
                    $this->add_message(Translation::get('ObjectNotImported'), self::TYPE_ERROR);
                }
            }
        }
        else
        {
            $this->add_message(Translation::get('WebpageObjectNotAvailable'), self::TYPE_WARNING);
        }
    }

    public static function is_available()
    {
        return in_array(self::FORMAT, DataManager::get_registered_types(true));
    }

    /**
     *
     * @return integer
     */
    public function determine_parent_id()
    {
        if ($this->get_parameters()->getWorkspace() instanceof PersonalWorkspace)
        {
            return $this->get_parameters()->get_category();
        }
        else
        {
            return 0;
        }
    }

    /**
     *
     * @param ContentObject $contentObject
     */
    public function process_workspace(ContentObject $contentObject)
    {
        if ($this->get_parameters()->getWorkspace() instanceof Workspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelationService->createContentObjectRelation(
                $this->get_parameters()->getWorkspace()->getId(),
                $contentObject->get_object_number(),
                $this->get_parameters()->get_category());
        }
    }
}
