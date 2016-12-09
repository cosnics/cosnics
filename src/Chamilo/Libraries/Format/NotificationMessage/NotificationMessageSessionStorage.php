<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

use Chamilo\Libraries\Platform\Session\Session;

/**
 * Stores user notification messages into the session
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessageSessionStorage implements NotificationMessageStorageInterface
{
    const PARAM_NOTIFICATION_MESSAGES = 'notification_messages';
    const PARAM_TYPE = 'type';
    const PARAM_MESSAGE = 'message';
    const PARAM_CATEGORY = 'category';

    /**
     * Stores the notification messages
     * 
     * @param NotificationMessage[] $notificationMessages
     */
    public function store($notificationMessages = array())
    {
        $notificationMessagesAsArray = array();
        
        foreach ($notificationMessages as $notificationMessage)
        {
            $notificationMessagesAsArray[] = array(
                self::PARAM_TYPE => $notificationMessage->getType(), 
                self::PARAM_MESSAGE => $notificationMessage->getMessage(), 
                self::PARAM_CATEGORY => $notificationMessage->getCategory());
        }
        
        Session::register(self::PARAM_NOTIFICATION_MESSAGES, $notificationMessagesAsArray);
    }

    /**
     * Retrieves the notification messages
     * 
     * @return NotificationMessage[]
     */
    public function retrieve()
    {
        $notificationMessagesAsArray = Session::get(self::PARAM_NOTIFICATION_MESSAGES, array());
        
        $notificationMessages = array();
        
        foreach ($notificationMessagesAsArray as $notificationMessageArray)
        {
            $notificationMessages[] = new NotificationMessage(
                $notificationMessageArray[self::PARAM_MESSAGE], 
                $notificationMessageArray[self::PARAM_TYPE], 
                $notificationMessageArray[self::PARAM_CATEGORY]);
        }
        
        return $notificationMessages;
    }

    /**
     * Clears the notification messages
     */
    public function clear()
    {
        Session::unregister(self::PARAM_NOTIFICATION_MESSAGES);
    }
}