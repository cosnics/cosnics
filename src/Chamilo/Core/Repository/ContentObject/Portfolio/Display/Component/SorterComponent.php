<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SorterComponent extends Manager implements DelegateComponent
{

    /**
     * Executes this component
     */
    public function run()
    {
        if ($this->canEditComplexContentObjectPathNode($this->get_current_node()))
        {
            $direction = Request::get(self::PARAM_SORT, self::SORT_UP);
            $selected_complex_content_object_item = $this->get_current_complex_content_object_item();
            $content_object = $this->get_current_content_object();
            
            if ($direction == self::SORT_UP && $this->get_current_node()->is_first_child() ||
                 $direction == self::SORT_DOWN && $this->get_current_node()->is_last_child())
            {
                $success = false;
            }
            else
            {
                $display_order = $selected_complex_content_object_item->get_display_order();
                $new_place = ($display_order + ($direction == self::SORT_UP ? - 1 : 1));
                $selected_complex_content_object_item->set_display_order($new_place);
                
                $succes = $selected_complex_content_object_item->update();
                
                if ($succes)
                {
                    $new_content_object_ids_path = $this->get_current_node()->get_parents_content_object_ids(true, true);
                    
                    $this->get_root_content_object()->get_complex_content_object_path()->reset();
                    $new_node = $this->get_root_content_object()->get_complex_content_object_path()->follow_path_by_content_object_ids(
                        $new_content_object_ids_path);
                    
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
            }
            
            $message = htmlentities(
                Translation::get(
                    ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'), 
                    array('OBJECT' => Translation::get('ContentObject')), 
                    StringUtilities::LIBRARIES));
            
            $parameters = [];
            
            if ($succes)
            {
                $parameters[self::PARAM_STEP] = $new_node->get_id();
            }
            else
            {
                $parameters[self::PARAM_STEP] = $this->get_current_node()->get_id();
            }
            
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
            
            $this->redirectWithMessage($message, (! $succes), $parameters);
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
