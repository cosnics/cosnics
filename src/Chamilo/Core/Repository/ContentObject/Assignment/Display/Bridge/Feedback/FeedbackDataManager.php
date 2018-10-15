<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Feedback;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackDataManagerInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Feedback
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackDataManager implements FeedbackDataManagerInterface
{
    /**
     * @var AssignmentFeedbackDataManagerInterface
     */
    protected $assignmentFeedbackDataManager;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    protected $entry;

    /**
     * FeedbackDataManager constructor.
     *
     * @param AssignmentFeedbackDataManagerInterface $assignmentFeedbackDataManager
     */
    public function __construct(AssignmentFeedbackDataManagerInterface $assignmentFeedbackDataManager)
    {
        $this->assignmentFeedbackDataManager = $assignmentFeedbackDataManager;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     */
    public function setEntry(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $feedback
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback
     */
    public function createFeedback(User $user, $feedback)
    {
        return $this->assignmentFeedbackDataManager->createFeedback($user, $feedback, $this->entry);
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(Feedback $feedback)
    {
        $this->assignmentFeedbackDataManager->updateFeedback($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback)
    {
        $this->assignmentFeedbackDataManager->deleteFeedback($feedback);
    }

    /**
     * @param int $count
     * @param int $offset
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback[]|\Chamilo\Libraries\Storage\ResultSet\DataClassResultSet|mixed[]
     */
    public function getFeedback($count = null, $offset = null)
    {
       return $this->assignmentFeedbackDataManager->getFeedbackByEntry($this->entry);
    }

    /**
     * @return int
     */
    public function countFeedback()
    {
        return $this->assignmentFeedbackDataManager->countFeedbackByEntry($this->entry);
    }

    /**
     * @param int $feedbackId
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function getFeedbackById($feedbackId)
    {
        return $this->assignmentFeedbackDataManager->getFeedbackByIdentifier($feedbackId);
    }
}