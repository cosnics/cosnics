<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Component;

use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component that allows the removal of items from the portfolio (folder)
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
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
            
            if ($this->canEditComplexContentObjectPathNode($selected_node->get_parent()))
            {
                $available_nodes[] = $selected_node;
            }
        }
        
        if (count($available_nodes) == 0)
        {
            $parameters = array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT);
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
        
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
        
        $this->redirect(
            Translation::get(
                $failures > 0 ? 'ObjectsNotDeleted' : 'ObjectsDeleted', 
                array('OBJECTS' => Translation::get('ComplexContentObjectItems')), 
                Utilities::COMMON_LIBRARIES), 
            $failures > 0, 
            array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT));
    }
}
