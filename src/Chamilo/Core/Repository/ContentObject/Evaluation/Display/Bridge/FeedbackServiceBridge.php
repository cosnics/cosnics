<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Interfaces\NotificationServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Bridge\Storage\DataClass\Entry;
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
    protected $entryId;
/*    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface
     */
/*    protected $assignmentFeedbackServiceBridge;

/*    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
/*    protected $entry;

/*    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface
     */
/*    protected $notificationServiceBridge;*/

/*    /**
     * FeedbackServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface $assignmentFeedbackServiceBridge
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\NotificationServiceBridgeInterface $notificationServiceBridge
     */
/*    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface $assignmentFeedbackServiceBridge,
        NotificationServiceBridgeInterface $notificationServiceBridge
    )
    {
        $this->assignmentFeedbackServiceBridge = $assignmentFeedbackServiceBridge;
        $this->notificationServiceBridge = $notificationServiceBridge;
    }*/
    /**
     * @param \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\FeedbackService $feedbackService
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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
     *
     * @return \Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback
     */
    public function createFeedback(
        User $user, \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
    )
    {
        $feedbackContentObject = $this->feedbackService->createFeedback($user, $feedbackContentObject, $this->entryId);
        return $feedbackContentObject;

        /*$feedbackContentObject = $this->assignmentFeedbackServiceBridge->createFeedback($user, $feedbackContentObject, $this->entry);
        if($feedbackContentObject instanceof \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback)
        {
            $this->notificationServiceBridge->createNotificationForNewFeedback($user, $this->entry, $feedbackContentObject);
        }

        return $feedbackContentObject;*/
        return null;
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(Feedback $feedback)
    {
        $this->feedbackService->updateFeedback($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback|\Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback $feedback
     *
     * @throws \Exception
     */
    public function deleteFeedback(Feedback $feedback)
    {
        $this->feedbackService->deleteFeedback($feedback);
    }

    /**
     * @param int $count
     * @param int $offset
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback[]|\Chamilo\Libraries\Storage\ResultSet\DataClassResultSet|mixed[]
     */
    public function getFeedback($count = null, $offset = null)
    {
        return $this->feedbackService->findFeedbackByEntryId($this->entryId);
    }

    /**
     * @return int
     */
    public function countFeedback()
    {
        //var_dump('countFeedback');
        //return $this->assignmentFeedbackServiceBridge->countFeedbackByEntry($this->entry);
        return 0;
    }

    /**
     * @param int $feedbackId
     *
     * @return \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
     */
    public function getFeedbackById($feedbackId)
    {
        return $this->feedbackService->findFeedbackById($feedbackId);
    }
}