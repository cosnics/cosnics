<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Feedback
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackServiceBridge implements FeedbackServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface
     */
    protected $assignmentFeedbackServiceBridge;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    protected $entry;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface
     */
    protected $notificationServiceBridge;

    /**
     * FeedbackServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface $assignmentFeedbackServiceBridge
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface $notificationServiceBridge
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface $assignmentFeedbackServiceBridge,
        NotificationServiceBridgeInterface $notificationServiceBridge
    )
    {
        $this->assignmentFeedbackServiceBridge = $assignmentFeedbackServiceBridge;
        $this->notificationServiceBridge = $notificationServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $feedbackContentObject
     * @param bool $isPrivate
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function createFeedback(
        User $user, \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject, bool $isPrivate = false
    )
    {
        $feedbackContentObject = $this->assignmentFeedbackServiceBridge->createFeedback($user, $feedbackContentObject, $this->entry);
        if($feedbackContentObject instanceof \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback)
        {
            $this->notificationServiceBridge->createNotificationForNewFeedback($user, $this->entry, $feedbackContentObject);
        }

        return $feedbackContentObject;
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(Feedback $feedback)
    {
        $this->assignmentFeedbackServiceBridge->updateFeedback($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback)
    {
        $this->assignmentFeedbackServiceBridge->deleteFeedback($feedback);
    }

    /**
     * @param int $count
     * @param int $offset
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback[]|\Chamilo\Libraries\Storage\ResultSet\DataClassResultSet|mixed[]
     */
    public function getFeedback($count = null, $offset = null)
    {
       return $this->assignmentFeedbackServiceBridge->getFeedbackByEntry($this->entry);
    }

    /**
     * @return int
     */
    public function countFeedback()
    {
        return $this->assignmentFeedbackServiceBridge->countFeedbackByEntry($this->entry);
    }

    /**
     * @param int $feedbackId
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function getFeedbackById($feedbackId)
    {
        return $this->assignmentFeedbackServiceBridge->getFeedbackByIdentifier($feedbackId);
    }

    public function supportsPrivateFeedback()
    {
        return false;
    }
}