<?php
namespace Chamilo\Libraries\UserInterface\NotificationMessage\Storage\Repository;

use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Interface\NotificationMessageStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Storage\Repository
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

    public function clear(): void
    {
        $this->getSession()->remove(self::PARAM_NOTIFICATION_MESSAGES);
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage[]
     */
    public function retrieve(): array
    {
        $notificationMessagesAsArray = $this->getSession()->get(self::PARAM_NOTIFICATION_MESSAGES, []);

        $notificationMessages = [];

        foreach ($notificationMessagesAsArray as $notificationMessageArray) {
            $notificationMessages[] = new NotificationMessage(
                $notificationMessageArray[self::PARAM_MESSAGE], $notificationMessageArray[self::PARAM_TYPE],
                $notificationMessageArray[self::PARAM_CATEGORY]
            );
        }

        return $notificationMessages;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage[] $notificationMessages
     */
    public function store(array $notificationMessages = []): void
    {
        $notificationMessagesAsArray = [];

        foreach ($notificationMessages as $notificationMessage) {
            $notificationMessagesAsArray[] = [
                self::PARAM_TYPE => $notificationMessage->getType(),
                self::PARAM_MESSAGE => $notificationMessage->getMessage(),
                self::PARAM_CATEGORY => $notificationMessage->getCategory()
            ];
        }

        $this->getSession()->set(self::PARAM_NOTIFICATION_MESSAGES, $notificationMessagesAsArray);
    }
}