<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

//use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback;
use Chamilo\Core\Repository\Feedback\Bridge\FeedbackServiceBridgeInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\FeedbackService;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Bridge\Feedback
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class FeedbackServiceBridge implements FeedbackServiceBridgeInterface
{
    /**
     * @var integer
     */
    protected $entryId;

    /**
     * @var FeedbackService
     */
    protected $feedbackService;

    // protected $notificationServiceBridge;

    /**
     * @param FeedbackService $feedbackService
     */
    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService = $feedbackService;
    }

    /**
     * @param int $entryId
     */
    public function setEntryId(int $entryId)
    {
        $this->entryId = $entryId;
    }

    /**
     * @param int $count
     * @param int $offset
     *
     * @return Feedback[]|\Chamilo\Libraries\Storage\ResultSet\DataClassResultSet|mixed[]
     */
    public function getFeedback($count = null, $offset = null)
    {
        return $this->feedbackService->findFeedbackByEntryId($this->entryId);
    }

    /**
     * @param int $feedbackId
     *
     * @return Feedback
     */
    public function getFeedbackById($feedbackId)
    {
        return $this->feedbackService->findFeedbackById($feedbackId);
    }

    /**
     * @return int
     */
    public function countFeedback(): int
    {
        //return $this->assignmentFeedbackServiceBridge->countFeedbackByEntry($this->entry);
        return 0;
    }

    /**
     * @param User $user
     * @param \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
     * @param bool $isPrivate
     *
     * @return EvaluationEntryFeedback
     */
    public function createFeedback(
        User $user, \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject, bool $isPrivate = false
    )
    {
        return $this->feedbackService->createFeedback($user, $feedbackContentObject, $this->entryId, $isPrivate);

        // Todo: Notification code left here for future implementation
        /*$feedbackContentObject = $this->assignmentFeedbackServiceBridge->createFeedback($user, $feedbackContentObject, $this->entry);
        if($feedbackContentObject instanceof \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback)
        {
            $this->notificationServiceBridge->createNotificationForNewFeedback($user, $this->entry, $feedbackContentObject);
        }

        return $feedbackContentObject;*/
    }

    /**
     * @param Feedback|EvaluationEntryFeedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(Feedback $feedback)
    {
        $this->feedbackService->updateFeedback($feedback);
    }

    /**
     * @param Feedback|EvaluationEntryFeedback $feedback
     *
     * @throws \Exception
     */
    public function deleteFeedback(Feedback $feedback)
    {
        $this->feedbackService->deleteFeedback($feedback);
    }

    public function supportsPrivateFeedback()
    {
        return true;
    }
}