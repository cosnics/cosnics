<?php
namespace Chamilo\Core\Repository\Feedback;

/**
 * Interface which indicates a component implements the Feedback manager
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface FeedbackNotificationSupport
{

    /**
     * Retrieve the Notification instance
     * 
     * @return \core\repository\content_object\portfolio\feedback\Notification
     */
    public function retrieve_notification();

    /**
     * Returns an newly instantiated Notification object
     * 
     * @return \core\repository\content_object\portfolio\feedback\Notification
     */
    public function get_notification();
}
