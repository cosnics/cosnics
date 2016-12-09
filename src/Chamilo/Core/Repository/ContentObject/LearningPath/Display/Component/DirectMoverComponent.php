<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * A mover component which moves the selected object directly to a different parent
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DirectMoverComponent extends Manager
{

    function run()
    {
        $this->validateAndFixCurrentStep();
        
        $currentNode = $this->get_current_node();
        if (! $this->canEditComplexContentObjectPathNode($currentNode))
        {
            throw new NotAllowedException();
        }
        
        $parentId = $this->getRequest()->get(self::PARAM_PARENT_ID);
        $displayOrder = $this->getRequest()->get(self::PARAM_DISPLAY_ORDER);
        
        if (! $parentId || ! $displayOrder)
        {
            throw new \RuntimeException('For the direct mover to work you need to specify a parent and a display order');
        }
        $path = $this->get_root_content_object()->get_complex_content_object_path();
        $parentNode = $path->get_node($parentId);
        
        $parentContentObjectId = $parentNode->get_content_object()->getId();
        
        $complexContentObjectItem = $currentNode->get_complex_content_object_item();
        
        $complexContentObjectItem->set_parent($parentContentObjectId);
        $complexContentObjectItem->set_display_order($displayOrder);
        $success = $complexContentObjectItem->update();
        
        if ($success)
        {
            $content_object = $currentNode->get_content_object();
            
            Event::trigger(
                'Activity', 
                \Chamilo\Core\Repository\Manager::context(), 
                array(
                    Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATED, 
                    Activity::PROPERTY_USER_ID => $this->get_user_id(), 
                    Activity::PROPERTY_DATE => time(), 
                    Activity::PROPERTY_CONTENT_OBJECT_ID => $content_object->getId(), 
                    Activity::PROPERTY_CONTENT => $content_object->get_title()));
            
            $parentNodeContentObjectIds = $parentNode->get_parents_content_object_ids(true, true);
            
            $parentNodeContentObjectIds[] = $content_object->get_id();
            
            $this->get_root_content_object()->get_complex_content_object_path()->reset();
            $new_node = $this->get_root_content_object()->get_complex_content_object_path()->follow_path_by_content_object_ids(
                $parentNodeContentObjectIds);
        }
        
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectUpdated' : 'ObjectNotUpdated'), 
                array('OBJECT' => Translation::get('ContentObject')), 
                Utilities::COMMON_LIBRARIES));
        
        $parameters = array();
        
        if ($success)
        {
            $parameters[self::PARAM_STEP] = $new_node->get_id();
        }
        else
        {
            $parameters[self::PARAM_STEP] = $this->get_current_node()->get_id();
        }
        
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
        
        $this->redirect($message, (! $success), $parameters, array(self::PARAM_CONTENT_OBJECT_ID));
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_STEP, self::PARAM_FULL_SCREEN, self::PARAM_CONTENT_OBJECT_ID);
    }
}
