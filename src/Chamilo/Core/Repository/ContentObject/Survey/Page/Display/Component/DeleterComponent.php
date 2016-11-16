<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

class DeleterComponent extends Manager
{

    /**
     * Executes this component
     */
    public function run()
    {
        $selected_steps = $this->getRequest()->get(self::PARAM_STEP);
        if (! is_array($selected_steps))
        {
            $selected_steps = array($selected_steps);
        }
        
        $path = $this->get_root_content_object()->get_complex_content_object_path();
        
        $available_nodes = array();
        
        foreach ($selected_steps as $selected_step)
        {
            $selected_node = $path->get_node($selected_step);
            
            if ($this->get_parent()->is_allowed_to_edit_content_object($selected_node->get_parent()))
            {
                $available_nodes[] = $selected_node;
            }
        }
        
        if (count($available_nodes) == 0)
        {
            $parameters = array(
                self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                self::PARAM_STEP => $this->get_current_node()->get_parent()->get_id());
            
            $this->redirect(
                Translation::get(
                    'NoObjectsToDelete', 
                    array('OBJECTS' => Translation::get('ComplexContentObjectItems')), 
                    Utilities::COMMON_LIBRARIES), 
                true, 
                $parameters);
        }
        
        $failures = 0;
        
        foreach ($available_nodes as $available_node)
        {
            $complex_content_object_item_id = $available_node->get_complex_content_object_item()->get_id();
            $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ComplexContentObjectItem::class_name(), 
                $complex_content_object_item_id);
            
            $current_parents_content_object_ids = $available_node->get_parents_content_object_ids(false, true);
            
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Configuration::class_name(), Configuration::PROPERTY_PAGE_ID), 
                new StaticConditionVariable($this->get_root_content_object()->get_id()));
            $configurations = DataManager::retrieves(
                Configuration::class_name(), 
                new DataClassRetrievesParameters($condition));
            while ($configuration = $configurations->next_result())
            {
                
                if ($configuration->getComplexQuestionId() == $complex_content_object_item_id)
                {
                    $configuration->delete();
                }
                else
                {
                    $toVisibleQuestionIds = $configuration->getToVisibleQuestionIds();
                    $newToVisibleQuestionIds = array_diff($toVisibleQuestionIds, array($complex_content_object_item_id));
                    if (count($newToVisibleQuestionIds) == 0)
                    {
                        $configuration->delete();
                    }
                    else
                    {
                        $configuration->setToVisibleQuestionIds($newToVisibleQuestionIds);
                        $configuration->update();
                    }
                }
            }
            
            $success = $complex_content_object_item->delete();
            
            if ($success)
            {
                Event::trigger(
                    'Activity', 
                    \Chamilo\Core\Repository\Manager::context(), 
                    array(
                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_DELETE_ITEM, 
                        Activity::PROPERTY_USER_ID => $this->get_user_id(), 
                        Activity::PROPERTY_DATE => time(), 
                        Activity::PROPERTY_CONTENT_OBJECT_ID => $available_node->get_parent()->get_content_object()->get_id(), 
                        Activity::PROPERTY_CONTENT => $available_node->get_parent()->get_content_object()->get_title() .
                             ' > ' . $available_node->get_content_object()->get_title()));
            }
            else
            {
                $failures ++;
            }
        }
        
        $this->redirect(
            Translation::get(
                $failures > 0 ? 'ObjectsNotDeleted' : 'ObjectsDeleted', 
                array('OBJECTS' => Translation::get('ComplexContentObjectItems')), 
                Utilities::COMMON_LIBRARIES), 
            $failures > 0, 
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT, 
                self::PARAM_STEP => $this->get_current_node()->get_parent()->get_id()));
    }
}
