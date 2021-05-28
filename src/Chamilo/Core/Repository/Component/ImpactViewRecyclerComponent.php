<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Renderer\ImpactViewRenderer;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Component to view the impact of a delete command
 * 
 * @author Tom Goethals - Hogeschool Gent
 */
class ImpactViewRecyclerComponent extends Manager
{

    /**
     *
     * @var ImpactViewRenderer
     */
    private $impact_view_renderer;

    /**
     * **************************************************************************************************************
     * Main component functionality
     * **************************************************************************************************************
     */
    
    /**
     * Runs this component and displays its output if applicable.
     */
    public function run()
    {
        $co_ids = $this->get_selected_co_ids();
        if (! is_array($co_ids))
        {
            $co_ids = array($co_ids);
        }
        
        $has_impact = $this->has_impact($co_ids);
        $this->impact_view_renderer = new ImpactViewRenderer($this, $co_ids, $has_impact);
        
        if ($this->impact_view_renderer->validated())
        {
            $this->handle_validated_co_ids($co_ids);
        }
        else
        {
            echo $this->impact_view_renderer->render($this->get_content_objects_condition($co_ids));
        }
    }

    public function get_parameters($include_search = false)
    {
        return array_merge(
            array(self::PARAM_CATEGORY_ID => $this->getRequest()->get(self::PARAM_CATEGORY_ID)), 
            parent::get_parameters($include_search));
    }

    /**
     * Handles the deletion of the selected content objects.
     * 
     * @param array $co_ids
     *
     * @throws DataClassNoResultException
     */
    private function handle_validated_co_ids(array $co_ids)
    {
        $failures = 0;
        
        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObjectPublication::PROPERTY_ID),
            $co_ids);
        
        $parameters = new DataClassRetrievesParameters($condition);
        
        $objects = DataManager::retrieves(ContentObject::class, $parameters);

        $publicationAggregator = $this->getPublicationAggregator();

        foreach($objects as $content_object)
        {
            $versions = $content_object->get_content_object_versions();

            $canUnlinkVersions = true;
            foreach($versions as $version)
            {
                if(!$publicationAggregator->canContentObjectBeUnlinked($version))
                {
                    $canUnlinkVersions = false;
                    break;
                }
            }

            if(!$canUnlinkVersions)
            {
                $failures++;
                continue;
            }

            foreach ($versions as $version)
            {
                if (! $version->delete_links())
                {
                    $failures ++;
                    continue;
                }
                if (! $version->recycle())
                {
                    $failures ++;
                }
                else
                {
                    Event::trigger(
                        'Activity', 
                        Manager::context(), 
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_RECYCLE, 
                            Activity::PROPERTY_USER_ID => $this->get_user_id(), 
                            Activity::PROPERTY_DATE => time(), 
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $version->get_id(), 
                            Activity::PROPERTY_CONTENT => $version->get_title()));
                }
            }
        }
        
        $result = $this->get_result(
            $failures, 
            count($co_ids), 
            'ContentObjectNotDeleted', 
            'ContentObjectsNotDeleted', 
            'ContentObjectDeleted', 
            'ContentObjectsDeleted');
        
        $this->redirect(
            $result, 
            $failures > 0, 
            array(
                self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS, 
                self::PARAM_CATEGORY_ID => $this->getRequest()->get(self::PARAM_CATEGORY_ID)));
    }

    /**
     * Builds a result message with given parameters
     * 
     * @param int $failures
     * @param int $count
     * @param string $fail_message_single
     * @param string $fail_message_multiple
     * @param string $succes_message_single
     * @param string $succes_message_multiple
     *
     * @return string
     */
    public function get_result($failures, $count, $fail_message_single, $fail_message_multiple, $succes_message_single, 
        $succes_message_multiple, $context = null)
    {
        if ($failures)
        {
            $message = $count == 1 ? $fail_message_single : $fail_message_multiple;
        }
        else
        {
            $message = $count == 1 ? $succes_message_single : $succes_message_multiple;
        }
        
        return Translation::getInstance()->getTranslation($message, [], Manager::context());
    }

    protected function get_selected_co_ids()
    {
        return $this->getRequest()->get(self::PARAM_CONTENT_OBJECT_ID);
    }

    /**
     * **************************************************************************************************************
     * Inherited
     * **************************************************************************************************************
     */
    
    /**
     * Checks if the selected content object(id)s are clear for deletion.
     * 
     * @param array $selected_ids
     *
     * @return bool
     */
    public function has_impact(array $selected_ids = [])
    {
        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObjectPublication::PROPERTY_ID),
            $selected_ids);
        
        $parameters = new DataClassRetrievesParameters($condition);
        
        $objects = DataManager::retrieves(ContentObject::class, $parameters);

        $publicationAggregator = $this->getPublicationAggregator();
        
        $failed = 0;
        foreach($objects as $content_object)
        {
            if (! DataManager::content_object_deletion_allowed($content_object))
            {
                $failed ++;
                continue;
            }

            if(!$publicationAggregator->canContentObjectBeUnlinked($content_object))
            {
                $failed++;
            }
        }
        
        return $failed > 0;
    }

    /**
     * Gets a datamanager condition to handle the specified content object ids.
     * 
     * @param array $selected_ids
     *
     * @return Condition
     */
    public function get_content_objects_condition(array $selected_ids)
    {
        $conditions = [];
        foreach ($selected_ids as $selected_co_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                new StaticConditionVariable($selected_co_id));
        }
        
        return new AndCondition(
            new OrCondition($conditions), 
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
                new StaticConditionVariable(ContentObject::STATE_NORMAL)));
    }
}