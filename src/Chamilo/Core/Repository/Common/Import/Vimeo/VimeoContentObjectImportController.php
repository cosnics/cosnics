<?php
namespace Chamilo\Core\Repository\Common\Import\Vimeo;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo;
use Chamilo\Core\Repository\External\DataConnector;
use Chamilo\Core\Repository\External\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class VimeoContentObjectImportController extends ContentObjectImportController
{
    const FORMAT = 'Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo';

    public function run()
    {
        $url = $this->get_parameters()->get_url();
        
        if (self::is_available())
        {
            $url_parts = parse_url($url);
            
            parse_str($url_parts['query'], $url_query);
            
            if (strpos($url_parts['host'], 'vimeo.com') !== false)
            {
                $external_id = substr($url_parts['path'], 1);
                
                if (! is_numeric($external_id))
                {
                    $this->add_message(Translation::get('ObjectNotImported'), self::TYPE_ERROR);
                }
            }
            else
            {
                $this->add_message(Translation::get('ObjectNotImported'), self::TYPE_ERROR);
            }
            
            if (! $this->has_messages(self::TYPE_ERROR))
            {
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        Instance::class,
                        Instance::PROPERTY_TYPE),
                    new StaticConditionVariable(
                        Manager::get_namespace(self::FORMAT)));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        Instance::class,
                        Instance::PROPERTY_ENABLED),
                    new StaticConditionVariable(1));
                $condition = new AndCondition($conditions);
                
                $external_repositories = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieves(
                    Instance::class,
                    new DataClassRetrievesParameters($condition));
                
                $external_repository = $external_repositories->current();
                $vimeo_connector = DataConnector::getInstance($external_repository);
                $external_object = $vimeo_connector->retrieve_external_repository_object($external_id);
                
                $vimeo = ContentObject::factory(Vimeo::get_type_name());
                $vimeo->set_title($external_object->get_title());
                $vimeo->set_description($external_object->get_description());
                $vimeo->set_owner_id($this->get_parameters()->get_user());
                $vimeo->set_parent_id($this->determine_parent_id());
                
                if ($vimeo->create())
                {
                    $this->process_workspace($vimeo);
                    
                    SynchronizationData::quicksave(
                        $vimeo, 
                        $external_object, 
                        $external_repository->get_id());
                    $this->add_message(Translation::get('ObjectImported'), self::TYPE_CONFIRM);
                    return array($vimeo->get_id());
                }
                else
                {
                    $this->add_message(Translation::get('ObjectNotImported'), self::TYPE_ERROR);
                }
            }
        }
        else
        {
            $this->add_message(Translation::get('VimeoObjectNotAvailable'), self::TYPE_WARNING);
        }
    }

    public static function is_available()
    {
        $vimeo_object_available = in_array(self::FORMAT, DataManager::get_registered_types(true));
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Instance::class,
                Instance::PROPERTY_TYPE),
            new StaticConditionVariable(Manager::get_namespace(self::FORMAT)));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Instance::class,
                Instance::PROPERTY_ENABLED),
            new StaticConditionVariable(1));
        $condition = new AndCondition($conditions);
        
        $external_repositories = \Chamilo\Core\Repository\Instance\Storage\DataManager::retrieves(
            Instance::class,
            new DataClassRetrievesParameters($condition));
        $vimeo_connector_available = $external_repositories->count() == 1;
        
        return $vimeo_object_available && $vimeo_connector_available;
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
                $contentObject->getId(), 
                $this->get_parameters()->get_category());
        }
    }
}
