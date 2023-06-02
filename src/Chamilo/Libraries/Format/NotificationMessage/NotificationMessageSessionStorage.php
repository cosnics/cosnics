<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Stores user notification messages into the session
 *
 * @package Chamilo\Libraries\Format\NotificationMessage
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessageSessionStorage implements NotificationMessageStorageInterface
{
    public const PARAM_CATEGORY = 'category';
    public const PARAM_MESSAGE = 'message';
    public const PARAM_NOTIFICATION_MESSAGES = 'notification_messages';
    public const PARAM_TYPE = 'type';

    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function clear()
    {
        $this->getSession()->remove(self::PARAM_NOTIFICATION_MESSAGES);
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @return \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[]
     */
    public function retrieve(): array
    {
        $notificationMessagesAsArray = $this->getSession()->get(self::PARAM_NOTIFICATION_MESSAGES, []);

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

        $this->getSession()->set(self::PARAM_NOTIFICATION_MESSAGES, $notificationMessagesAsArray);
    }
}