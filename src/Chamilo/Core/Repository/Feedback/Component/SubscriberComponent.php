<?php
namespace Chamilo\Core\Repository\Feedback\Component;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Notification;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * Component to remove the notification
 * 
 * @package repository\content_object\portfolio\feedback
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscriberComponent extends Manager
{

    /**
     * Executes this component
     */
    public function run()
    {
        try
        {
            if (! $this->get_parent()->is_allowed_to_view_feedback())
            {
                throw new NotAllowedException();
            }
            
            $notification = $this->get_parent()->retrieve_notification();
            
            if ($notification instanceof Notification)
            {
                $notification->set_modification_date(time());
                
                if (! $notification->update())
                {
                    throw new Exception(Translation::get('FeedbackNotificationNotUpdated'));
                }
            }
            elseif (! $notification instanceof Notification)
            {
                $notification = $this->get_parent()->get_notification();
                $notification->set_user_id($this->get_user_id());
                $notification->set_creation_date(time());
                $notification->set_modification_date(time());
                
                if (! $notification->create())
                {
                    throw new Exception(Translation::get('FeedbackNotificationNotAdded'));
                }
            }
            
            $success = true;
            $message = Translation::get('FeedbackNotificationAdded');
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}