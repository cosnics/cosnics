<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

/**
 * Manages notification messages
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessageManager
{

    /**
     *
     * @var NotificationMessageStorageInterface
     */
    protected $notificationMessageStorage;

    /**
     *
     * @var NotificationMessageRenderer
     */
    protected $notificationMessageRenderer;

    /**
     * NotificationMessageManager constructor.
     * 
     * @param NotificationMessageStorageInterface $notificationMessageStorage
     * @param NotificationMessageRenderer $notificationMessageRenderer
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
     * @param NotificationMessage $notificationMessage
     * @param int $limitByCategory - Limits the number of messages of the same category by the given number (0 =
     *            infinite)
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
     * @param NotificationMessage $notificationMessageToBeAdded
     * @param NotificationMessage[] $notificationMessages
     * @param int $limitByCategory
     *
     * @return bool
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