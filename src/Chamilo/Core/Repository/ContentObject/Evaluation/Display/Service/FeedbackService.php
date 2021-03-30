<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\FeedbackRepository;
use Chamilo\Core\User\Storage\DataClass\User;

class FeedbackService
{
    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\FeedbackRepository
     */
    protected $feedbackRepository;

    /**
     * FeedbackService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    public function findFeedbackByEntryId(int $entryId)
    {
        return $this->feedbackRepository->findFeedbackByEntryId($entryId);
    }

    public function findFeedbackById(int $feedbackId)
    {
        return $this->feedbackRepository->retrieveFeedbackById($feedbackId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject
     * @param integer $entry_id
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback
     */
    public function createFeedback(
        User $user, \Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback $feedbackContentObject,
        int $entry_id
    )
    {
        $feedbackObject = new EvaluationEntryFeedback();
        $feedbackObject->setEntryId($entry_id);

        $feedbackObject->set_user_id($user->getId());
        $feedbackObject->set_creation_date(time());
        $feedbackObject->set_modification_date(time());
        $feedbackObject->setFeedbackContentObjectId($feedbackContentObject->getId());

        if (!$this->feedbackRepository->createFeedback($feedbackObject))
        {
            throw new \RuntimeException('Could not create feedback in the database');
        }

        return $feedbackObject;
    }


    /**
     * @param Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback $feedback
     *
     * @throws \Exception
     */
    public function updateFeedback(EvaluationEntryFeedback $feedback)
    {
        if (!$this->feedbackRepository->updateFeedback($feedback))
        {
            throw new \RuntimeException('Could not update feedback in the database');
        }
    }

    /**
     * @param Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback $feedback
     *
     * @throws \Exception
     */
    public function deleteFeedback(EvaluationEntryFeedback $feedback)
    {
        if (!$this->feedbackRepository->deleteFeedback($feedback))
        {
            throw new \RuntimeException('Could not delete feedback in the database');
        }
    }

}

