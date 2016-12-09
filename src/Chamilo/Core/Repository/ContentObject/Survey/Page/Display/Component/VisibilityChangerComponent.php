<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class VisibilityChangerComponent extends TabComponent
{
    const MESSAGE_VISIBILITY_CHANGED = 'VisibilityChanged';
    const MESSAGE_VISIBILITY_NOT_CHANGED = 'VisibilityNotChanged';

    /**
     * Runs this component and displays its output.
     */
    function build()
    {
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            $selected_complex_content_object_item = $this->get_current_complex_content_object_item();
            $content_object = $this->get_current_content_object();
            
            $selected_complex_content_object_item->toggle_visibility();
            $succes = $selected_complex_content_object_item->update();
            
            if ($succes)
            {
                Event::trigger(
                    'Activity', 
                    \Chamilo\Core\Repository\Manager::context(), 
                    array(
                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATED, 
                        Activity::PROPERTY_USER_ID => $this->get_user_id(), 
                        Activity::PROPERTY_DATE => time(), 
                        Activity::PROPERTY_CONTENT_OBJECT_ID => $content_object->get_id(), 
                        Activity::PROPERTY_CONTENT => $content_object->get_title()));
            }
            
            $message = $succes ? self::MESSAGE_VISIBILITY_CHANGED : self::MESSAGE_VISIBILITY_NOT_CHANGED;
            
            $parameters = array();
            $parameters[self::PARAM_STEP] = $this->get_current_node()->get_id();
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
            
            $this->redirect($message, (! $succes), $parameters);
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}