<?php
namespace Chamilo\Core\Repository\Feedback;

use Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationService;
use Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationServiceInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'feedback_action';
    const PARAM_FEEDBACK_ID = 'feedback_id';
    
    // Actions
    const ACTION_BROWSE_V2 = 'BrowserV2';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_SUBSCRIBER = 'Subscriber';
    const ACTION_UNSUBSCRIBER = 'Unsubscriber';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const CONFIGURATION_SHOW_FEEDBACK_HEADER = 'showFeedbackHeader';

    /**
     * Returns the notification service
     * 
     * @return NotificationServiceInterface
     */
    public function getNotificationService()
    {
        $application = $this->get_application();
        
        if ($application instanceof FeedbackNotificationSupport)
        {
            return new NotificationService($application->get_notification_handlers());
        }
        
        return null;
    }

    /**
     * Notifies of a new feedback object
     * 
     * @param Feedback $feedback
     */
    public function notifyNewFeedback(Feedback $feedback)
    {
        $application = $this->get_application();
        
        if ($application instanceof FeedbackNotificationSupport)
        {
            $this->getNotificationService()->notify($feedback, $application->retrieve_notifications()->as_array());
        }
    }

    /**
     * @return bool
     */
    public function showFeedbackHeader()
    {
        $configuration = $this->getApplicationConfiguration()->get(self::CONFIGURATION_SHOW_FEEDBACK_HEADER);

        return isset($configuration) ? $configuration : true;
    }
}
