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

    /**
     *
     * @var \Chamilo\Libraries\Format\NotificationMessage\NotificationMessageStorageInterface
     */
    protected $notificationMessageStorage;

    /**
     *
     * @var \Chamilo\Libraries\Format\NotificationMessage\NotificationMessageRenderer
     */
    protected $notificationMessageRenderer;

    /**
     * NotificationMessageManager constructor.
     *
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessageStorageInterface $notificationMessageStorage
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessageRenderer $notificationMessageRenderer
     */
    public function __construct(NotificationMessageStorageInterface $notificationMessageStorage = null,
        NotificationMessageRenderer $notificationMessageRenderer = null)
    {
        if (is_null($notificationMessageStorage))
        {
            $notificationMessageStorage = new NotificationMessageSessionStorage();
        }

        if (is_null($notificationMessageRenderer))
        {
            $notificationMessageRenderer = new NotificationMessageRenderer();
        }

        $this->notificationMessageStorage = $notificationMessageStorage;
        $this->notificationMessageRenderer = $notificationMessageRenderer;
    }

    /**
     * Adds a message
     *
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage $notificationMessage
     * @param integer $limitByCategory - Limits the number of messages of the same category by the given number (0 =
     *        infinite)
     */
    public function addMessage(NotificationMessage $notificationMessage, $limitByCategory = 0)
    {
        $notificationMessages = $this->notificationMessageStorage->retrieve();

        if ($this->canAddMessage($notificationMessage, $notificationMessages, $limitByCategory))
        {
            $notificationMessages[] = $notificationMessage;
        }

        $this->notificationMessageStorage->store($notificationMessages);
    }

    /**
     * Renders the messages on the screen and clears them from the storage
     *
     * @return string
     */
    public function renderMessages()
    {
        $messages = $this->notificationMessageStorage->retrieve();

        $this->notificationMessageStorage->clear();

        return $this->notificationMessageRenderer->render($messages);
    }

    /**
     * Checks if a message can be added to the array of messages
     *
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage $notificationMessageToBeAdded
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[] $notificationMessages
     * @param integer $limitByCategory
     *
     * @return boolean
     */
    protected function canAddMessage(NotificationMessage $notificationMessageToBeAdded, $notificationMessages = array(),
        $limitByCategory = 0)
    {
        if ($limitByCategory > 0 && ! is_null($notificationMessageToBeAdded->getCategory()))
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
}