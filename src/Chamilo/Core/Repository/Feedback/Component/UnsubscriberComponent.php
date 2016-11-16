<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component to remove the notification
 * 
 * @package repository\content_object\portfolio\feedback
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UnsubscriberComponent extends Manager
{

    /**
     * Executes this component
     */
    public function run()
    {
        try
        {
            $notification = $this->get_parent()->retrieve_notification();
            
            if (! $this->get_parent()->is_allowed_to_view_feedback())
            {
                throw new NotAllowedException();
            }
            
            if (! $notification->delete())
            {
                throw new \Exception(
                    Translation::get(
                        'ObjectNotDeleted', 
                        array('OBJECT' => Translation::get('Notification')), 
                        Utilities::COMMON_LIBRARIES));
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectDeleted', 
                array('OBJECT' => Translation::get('Notification')), 
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