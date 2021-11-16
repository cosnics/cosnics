<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\FeedbackRepository;
use Chamilo\Core\Repository\ContentObject\Feedback\Storage\DataClass\Feedback;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class FeedbackService
{
    /**
     * @var FeedbackRepository
     */
    protected $feedbackRepository;

    /**
     * FeedbackService constructor.
     *
     * @param FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     * @param int $entryId
     *
     * @return DataClassIterator
     */
    public function findFeedbackByEntryId(int $entryId): DataClassIterator
    {
        return $this->feedbackRepository->findFeedbackByEntryId($entryId);
    }

    /**
     * @param int $feedbackId
     *
     * @return EvaluationEntryFeedback|DataClass
     */
    public function findFeedbackById(int $feedbackId)
    {
        return $this->feedbackRepository->retrieveFeedbackById($feedbackId);
    }

    /**
     * @param User $user
     * @param Feedback $feedbackContentObject
     * @param integer $entry_id
     * @param bool $isPrivate
     *
     * @return EvaluationEntryFeedback
     */
    public function createFeedback(User $user, Feedback $feedbackContentObject, int $entry_id, bool $isPrivate = false): EvaluationEntryFeedback
    {
        $feedbackObject = new EvaluationEntryFeedback();
        $feedbackObject->setEntryId($entry_id);

        $feedbackObject->set_user_id($user->getId());
        $feedbackObject->set_creation_date(time());
        $feedbackObject->set_modification_date(time());
        $feedbackObject->setFeedbackContentObjectId($feedbackContentObject->getId());
        $feedbackObject->setIsPrivate($isPrivate);

        if (!$this->feedbackRepository->createFeedback($feedbackObject))
        {
            throw new \RuntimeException('Could not create feedback in the database');
        }

        return $feedbackObject;
    }

    /**
     * @param EvaluationEntryFeedback $feedback
     */
    public function updateFeedback(EvaluationEntryFeedback $feedback)
    {
        if (!$this->feedbackRepository->updateFeedback($feedback))
        {
            throw new \RuntimeException('Could not update feedback in the database');
        }
    }

    /**
     * @param EvaluationEntryFeedback $feedback
     */
    public function deleteFeedback(EvaluationEntryFeedback $feedback)
    {
        if (!$this->feedbackRepository->deleteFeedback($feedback))
        {
            throw new \RuntimeException('Could not delete feedback in the database');
        }
    }
}

