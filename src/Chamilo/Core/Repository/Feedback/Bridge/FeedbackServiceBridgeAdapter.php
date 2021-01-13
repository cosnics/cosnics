<?php

namespace Chamilo\Core\Repository\Feedback\Bridge;

use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

/**
 * @package Chamilo\Core\Repository\Feedback\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @deprecated only used to provide a temporary solution between the old FeedbackSupport interface and the new bridge architecture. Implement your own FeedbackBridge
 */
class FeedbackServiceBridgeAdapter implements FeedbackServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\Feedback\FeedbackSupport
     */
    protected $feedbackSupportComponent;

    /**
     * FeedbackBridgeAdapter constructor.
     *
     * @param \Chamilo\Core\Repository\Feedback\FeedbackSupport $feedbackSupportComponent
     */
    public function __construct(FeedbackSupport $feedbackSupportComponent)
    {
        $this->feedbackSupportComponent = $feedbackSupportComponent;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function createFeedback(
        User $user, \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
    )
    {
        $feedbackObject = $this->feedbackSupportComponent->get_feedback();

        $feedbackObject->set_user_id($user->getId());
        $feedbackObject->set_comment($feedbackContentObject);
        $feedbackObject->set_creation_date(time());
        $feedbackObject->set_modification_date(time());
        $feedbackObject->setFeedbackContentObjectId($feedbackContentObject->getId());

        if (!$feedbackObject->create())
        {
            throw new \RuntimeException('Could not create feedback in the database');
        }

        return $feedbackObject;
    }

    /**
     * @param int $count
     * @param int $offset
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback[] | mixed[] | DataClassIterator
     */
    public function getFeedback($count = null, $offset = null)
    {
        $feedback = $this->feedbackSupportComponent->retrieve_feedbacks($count, $offset);
        if ($feedback instanceof DataClassIterator)
        {
            return $feedback;
        }

        return $feedback->as_array();
    }

    /**
     * @return int
     */
    public function countFeedback()
    {
        return $this->feedbackSupportComponent->count_feedbacks();
    }

    /**
     * @param int $feedbackId
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function getFeedbackById($feedbackId)
    {
        return $this->feedbackSupportComponent->retrieve_feedback($feedbackId);
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(Feedback $feedback)
    {
        if (!$feedback->update())
        {
            throw new \RuntimeException('Could not create feedback in the database');
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @throws \Exception
     */
    public function deleteFeedback(Feedback $feedback)
    {
        if (!$feedback->delete())
        {
            throw new \RuntimeException('Could not create feedback in the database');
        }
    }
}