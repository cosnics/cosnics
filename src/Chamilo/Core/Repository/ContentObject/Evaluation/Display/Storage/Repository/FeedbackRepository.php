<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class FeedbackRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param integer $entryId
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntryId(int $entryId)
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
     *
     * @param integer $feedbackId
     *
     * @return Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveFeedbackById(int $feedbackId)
    {
        return $this->dataClassRepository->retrieveById($this->getFeedbackClassName(), $feedbackId);
    }

    /**
     * @param Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback $feedback
     *
     * @return bool
     */
    public function createFeedback(EvaluationEntryFeedback $feedback)
    {
        return $this->dataClassRepository->create($feedback);
    }

    /**
     * @param Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback $feedback
     *
     * @return bool
     */
    public function updateFeedback(EvaluationEntryFeedback $feedback)
    {
        return $this->dataClassRepository->update($feedback);
    }

    /**
     * @param Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback $feedback
     *
     * @return bool
     */
    public function deleteFeedback(EvaluationEntryFeedback $feedback)
    {
        return $this->dataClassRepository->delete($feedback);
    }

    /**
     * @return string
     */
    private function getFeedbackClassName() : string
    {
        return EvaluationEntryFeedback::class_name();
    }
}
