<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge;

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
     * FeedbackServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface $assignmentFeedbackServiceBridge
     */
    public function __construct(Interfaces\FeedbackServiceBridgeInterface $assignmentFeedbackServiceBridge)
    {
        $this->assignmentFeedbackServiceBridge = $assignmentFeedbackServiceBridge;
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
     * @param string $feedback
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function createFeedback(User $user, $feedback)
    {
        return $this->assignmentFeedbackServiceBridge->createFeedback($user, $feedback, $this->entry);
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
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback>
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
}