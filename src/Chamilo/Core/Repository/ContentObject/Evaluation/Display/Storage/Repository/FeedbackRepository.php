<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class FeedbackRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param integer $entryId
     *
     * @return DataClassIterator
     */
    public function findFeedbackByEntryId(int $entryId): DataClassIterator
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), EvaluationEntryFeedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entryId)
        );

        return $this->dataClassRepository->retrieves(
            $this->getFeedbackClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param integer $feedbackId
     *
     * @return EvaluationEntryFeedback|DataClass
     */
    public function retrieveFeedbackById(int $feedbackId)
    {
        return $this->dataClassRepository->retrieveById($this->getFeedbackClassName(), $feedbackId);
    }

    /**
     * @param EvaluationEntryFeedback $feedback
     *
     * @return bool
     */
    public function createFeedback(EvaluationEntryFeedback $feedback): bool
    {
        return $this->dataClassRepository->create($feedback);
    }

    /**
     * @param EvaluationEntryFeedback $feedback
     *
     * @return bool
     */
    public function updateFeedback(EvaluationEntryFeedback $feedback): bool
    {
        return $this->dataClassRepository->update($feedback);
    }

    /**
     * @param EvaluationEntryFeedback $feedback
     *
     * @return bool
     */
    public function deleteFeedback(EvaluationEntryFeedback $feedback): bool
    {
        return $this->dataClassRepository->delete($feedback);
    }

    /**
     * @return string
     */
    private function getFeedbackClassName(): string
    {
        return EvaluationEntryFeedback::class_name();
    }
}
