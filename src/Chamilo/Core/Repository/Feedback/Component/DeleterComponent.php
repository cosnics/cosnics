<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to delete the feedback
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        $feedback_ids = Request::get(self::PARAM_FEEDBACK_ID);
        
        try
        {
            if (empty($feedback_ids))
            {
                throw new NoObjectSelectedException(Translation::get('Feedback'));
            }
            
            if (! is_array($feedback_ids))
            {
                $feedback_ids = array($feedback_ids);
            }
            
            foreach ($feedback_ids as $feedback_id)
            {
                $feedback = $this->feedbackBridge->getFeedbackById($feedback_id);
                
                if (! $this->feedbackRightsBridge->canDeleteFeedback($feedback))
                {
                    throw new NotAllowedException();
                }
                
                $this->feedbackBridge->deleteFeedback($feedback);
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectDeleted', 
                array('OBJECT' => Translation::get('Feedback')), 
                Utilities::COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}