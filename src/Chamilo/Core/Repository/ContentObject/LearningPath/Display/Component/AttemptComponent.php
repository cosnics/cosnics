<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component to list activity on a portfolio item
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttemptComponent extends Manager
{

    /**
     * Executes this component
     */
    public function run()
    {
        if (! $this->is_allowed_to_edit_attempt_data())
        {
            throw new NotAllowedException();
        }
        
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_REPORTING;
        
        if ($this->is_current_step_set())
        {
            $item_attempt_id = Request::get(self::PARAM_ITEM_ATTEMPT_ID);
            
            if (isset($item_attempt_id))
            {
                // Delete the given item attempt for the given step
                $item_attempt = $this->get_application()->retrieve_learning_path_item_attempt($item_attempt_id);
                
                if (! $item_attempt->delete())
                {
                    $is_error = true;
                    $message = Translation::get(
                        'ObjectNotDeleted', 
                        array('OBJECT' => Translation::get('LearningPathItemAttempt'), Utilities::COMMON_LIBRARIES));
                    $parameters[self::PARAM_STEP] = $this->get_current_step();
                }
                else
                {
                    $is_error = false;
                    $message = Translation::get(
                        'ObjectDeleted', 
                        array('OBJECT' => Translation::get('LearningPathItemAttempt'), Utilities::COMMON_LIBRARIES));
                    
                    if (count($this->get_current_node()->get_data()) > 1)
                    {
                        $parameters[self::PARAM_STEP] = $this->get_current_step();
                    }
                    else
                    {
                        $parameters[self::PARAM_STEP] = null;
                    }
                }
            }
            else
            {
                // Delete all item attempts for the given step
                $current_node = $this->get_current_node();
                $error_count = 0;
                
                foreach ($current_node->get_data() as $attempt)
                {
                    if (! $attempt->delete())
                    {
                        $error_count ++;
                    }
                }
                
                $is_error = $error_count > 0 ? true : false;
                $message = $this->get_general_result(
                    $error_count, 
                    count($current_node->get_data()), 
                    Translation::get('LearningPathItemAttempt'), 
                    Translation::get('LearningPathItemAttempts'), 
                    self::RESULT_TYPE_DELETED);
                
                $parameters[self::PARAM_STEP] = null;
            }
        }
        else
        {
            // Delete the entire learning path attempt
            $attempt = $this->get_application()->retrieve_learning_path_tracker();
            
            if (! $attempt->delete())
            {
                $is_error = true;
                $message = Translation::get(
                    'ObjectNotDeleted', 
                    array('OBJECT' => Translation::get('LearningPathAttempt'), Utilities::COMMON_LIBRARIES));
            }
            else
            {
                $is_error = false;
                $message = Translation::get(
                    'ObjectDeleted', 
                    array('OBJECT' => Translation::get('LearningPathAttempt'), Utilities::COMMON_LIBRARIES));
            }
            
            $parameters[self::PARAM_STEP] = null;
        }
        
        $this->redirect($message, $is_error, $parameters);
    }
}
