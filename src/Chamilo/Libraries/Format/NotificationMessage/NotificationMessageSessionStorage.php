<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

use Chamilo\Libraries\Platform\Session\SessionUtilities;

/**
 * Stores user notification messages into the session
 *
 * @package Chamilo\Libraries\Format\NotificationMessage
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessageSessionStorage implements NotificationMessageStorageInterface
{
    public const PARAM_CATEGORY = 'category';
    public const PARAM_MESSAGE = 'message';
    public const PARAM_NOTIFICATION_MESSAGES = 'notification_messages';
    public const PARAM_TYPE = 'type';

    protected SessionUtilities $sessionUtilities;

    public function __construct(SessionUtilities $sessionUtilities)
    {
        $this->sessionUtilities = $sessionUtilities;
    }

    public function clear()
    {
        $this->getSessionUtilities()->unregister(self::PARAM_NOTIFICATION_MESSAGES);
    }

    public function getSessionUtilities(): SessionUtilities
    {
        return $this->sessionUtilities;
    }

    /**
     * @return \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[]
     */
    public function retrieve(): array
    {
        $notificationMessagesAsArray = $this->getSessionUtilities()->get(self::PARAM_NOTIFICATION_MESSAGES, []);

        $notificationMessages = [];

        foreach ($notificationMessagesAsArray as $notificationMessageArray)
        {
            $notificationMessages[] = new NotificationMessage(
                $notificationMessageArray[self::PARAM_MESSAGE], $notificationMessageArray[self::PARAM_TYPE],
                $notificationMessageArray[self::PARAM_CATEGORY]
            );
        }

        return $notificationMessages;
    }

    /**
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[] $notificationMessages
     */
    public function store(array $notificationMessages = [])
    {
        $notificationMessagesAsArray = [];

        foreach ($notificationMessages as $notificationMessage)
        {
            $notificationMessagesAsArray[] = [
                self::PARAM_TYPE => $notificationMessage->getType(),
                self::PARAM_MESSAGE => $notificationMessage->getMessage(),
                self::PARAM_CATEGORY => $notificationMessage->getCategory()
            ];
        }

        $this->getSessionUtilities()->register(self::PARAM_NOTIFICATION_MESSAGES, $notificationMessagesAsArray);
    }
}