<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

/**
 * Manages notification messages
 *
 * @package Chamilo\Libraries\Format\NotificationMessage
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessageManager
{

    protected NotificationMessageRenderer $notificationMessageRenderer;

    protected NotificationMessageStorageInterface $notificationMessageStorage;

    public function __construct(
        NotificationMessageStorageInterface $notificationMessageStorage,
        NotificationMessageRenderer $notificationMessageRenderer
    )
    {
        $this->notificationMessageStorage = $notificationMessageStorage;
        $this->notificationMessageRenderer = $notificationMessageRenderer;
    }

    /**
     * @param int $limitByCategory Limits the number of messages of the same category by the given number (0 = infinite)
     */
    public function addMessage(NotificationMessage $notificationMessage, int $limitByCategory = 0)
    {
        $notificationMessages = $this->getNotificationMessageStorage()->retrieve();

        if ($this->canAddMessage($notificationMessage, $notificationMessages, $limitByCategory))
        {
            $notificationMessages[] = $notificationMessage;
        }

        $this->getNotificationMessageStorage()->store($notificationMessages);
    }

    /**
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[] $notificationMessages
     */
    protected function canAddMessage(
        NotificationMessage $notificationMessageToBeAdded, array $notificationMessages = [], int $limitByCategory = 0
    ): bool
    {
        if ($limitByCategory > 0 && !is_null($notificationMessageToBeAdded->getCategory()))
        {
            $numberOfMessagesFromSameCategory = 0;

            foreach ($notificationMessages as $notificationMessage)
            {
                if ($notificationMessage->getCategory() == $notificationMessageToBeAdded->getCategory())
                {
                    $numberOfMessagesFromSameCategory ++;
                }
            }

            if ($numberOfMessagesFromSameCategory >= $limitByCategory)
            {
                return false;
            }
        }

        return true;
    }

    public function getNotificationMessageRenderer(): NotificationMessageRenderer
    {
        return $this->notificationMessageRenderer;
    }

    public function getNotificationMessageStorage(): NotificationMessageStorageInterface
    {
        return $this->notificationMessageStorage;
    }

    /**
     * Renders the messages on the screen and clears them from the storage
     *
     * @return string
     */
    public function renderMessages(): string
    {
        $messages = $this->getNotificationMessageStorage()->retrieve();

        $this->getNotificationMessageStorage()->clear();

        return $this->getNotificationMessageRenderer()->render($messages);
    }
}