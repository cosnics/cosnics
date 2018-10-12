<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Feedback;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackBridgeInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Bridge\Feedback
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FeedbackBridge implements FeedbackBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    protected $dataProvider;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    protected $entry;

    /**
     * FeedbackBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $dataProvider
     */
    public function __construct(AssignmentDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
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
        $feedbackObject = $this->dataProvider->initializeFeedback();
        $feedbackObject->setEntryId($this->entry->getId());

        $feedbackObject->set_user_id($user->getId());
        $feedbackObject->set_comment($feedback);
        $feedbackObject->set_creation_date(time());
        $feedbackObject->set_modification_date(time());

        if(!$feedbackObject->create())
        {
            throw new \RuntimeException('Could not create feedback in the database');
        }

        return $feedbackObject;
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(Feedback $feedback)
    {
        if(!$feedback->update())
        {
            throw new \RuntimeException('Could not update feedback in the database');
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     */
    public function deleteFeedback(Feedback $feedback)
    {
        if(!$feedback->delete())
        {
            throw new \RuntimeException('Could not delete feedback in the database');
        }
    }

    /**
     * @param int $count
     * @param int $offset
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback[]|\Chamilo\Libraries\Storage\ResultSet\DataClassResultSet|mixed[]
     */
    public function getFeedback($count = null, $offset = null)
    {
        return $this->dataProvider->findFeedbackByEntry($this->entry);
    }

    /**
     * @return int
     */
    public function countFeedback()
    {
        return $this->dataProvider->countFeedbackByEntry($this->entry);
    }

    /**
     * @param int $feedbackId
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function getFeedbackById($feedbackId)
    {
        return $this->dataProvider->findFeedbackByIdentifier($feedbackId);
    }
}