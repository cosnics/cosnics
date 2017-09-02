<?php

namespace Chamilo\Core\Repository\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\AssessmentMatchTextQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass\AssessmentRatingQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Storage\DataClass\HotspotQuestion;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Storage\DataClass\OrderingQuestion;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Repository to retrieve the data to fix the content object resources
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceFixerRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * ContentObjectResourceFixerRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * Returns the list of content objects with a resource tag in their description field
     *
     * @param int $offset
     *
     * @return ContentObject[] | DataClassIterator
     */
    public function findContentObjectsWithResourceTags($offset = 0)
    {
        $conditions = [
            new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION),
                '*<resource*'
            ),
            new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION),
                '*data-co-id*'
            )
        ];

        $parameters = new DataClassRetrievesParameters(new OrCondition($conditions), 1000, $offset);

        return $this->dataClassRepository->retrieves(ContentObject::class_name(), $parameters);
    }

    /**
     * Counts the number of resource tags with a resource tag in their description field
     *
     * @return int
     */
    public function countContentObjectsWithResourceTags()
    {
        $conditions = [
            new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION),
                '*<resource*'
            ),
            new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION),
                '*data-co-id*'
            )
        ];

        $parameters = new DataClassCountParameters(new OrCondition($conditions));

        return $this->dataClassRepository->count(ContentObject::class_name(), $parameters);
    }

    /**
     * @return int
     */
    public function countAssessmentMatchingQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatchingQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return AssessmentMatchingQuestion[] | DataClassIterator
     */
    public function findAssessmentMatchingQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatchingQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countAssessmentMatchNumericQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatchNumericQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return AssessmentMatchNumericQuestion[] | DataClassIterator
     */
    public function findAssessmentMatchNumericQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatchNumericQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countAssessmentMatchTextQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatchTextQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return AssessmentMatchTextQuestion[] | DataClassIterator
     */
    public function findAssessmentMatchTextQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatchTextQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countAssessmentMatrixQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatrixQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return AssessmentMatrixQuestion[] | DataClassIterator
     */
    public function findAssessmentMatrixQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatrixQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countAssessmentMultipleChoiceQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMultipleChoiceQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return AssessmentMultipleChoiceQuestion[] | DataClassIterator
     */
    public function findAssessmentMultipleChoiceQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMultipleChoiceQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countAssessmentRatingQuestions()
    {
        return $this->dataClassRepository->count(AssessmentRatingQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return AssessmentRatingQuestion[] | DataClassIterator
     */
    public function findAssessmentRatingQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentRatingQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countAssessmentSelectQuestions()
    {
        return $this->dataClassRepository->count(AssessmentSelectQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return AssessmentSelectQuestion[] | DataClassIterator
     */
    public function findAssessmentSelectQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentSelectQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countFillInBlanksQuestions()
    {
        return $this->dataClassRepository->count(FillInBlanksQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return FillInBlanksQuestion[] | DataClassIterator
     */
    public function findFillInBlanksQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            FillInBlanksQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countForumPosts()
    {
        return $this->dataClassRepository->count(ForumPost::class_name());
    }

    /**
     * @param int $offset
     *
     * @return ForumPost[] | DataClassIterator
     */
    public function findForumPosts($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            ForumPost::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countHotspotQuestions()
    {
        return $this->dataClassRepository->count(HotspotQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return HotspotQuestion[] | DataClassIterator
     */
    public function findHotspotQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            HotspotQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countOrderingQuestions()
    {
        return $this->dataClassRepository->count(OrderingQuestion::class_name());
    }

    /**
     * @param int $offset
     *
     * @return OrderingQuestion[] | DataClassIterator
     */
    public function findOrderingQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            OrderingQuestion::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * @return int
     */
    public function countWorkspaces()
    {
        return $this->dataClassRepository->count(Workspace::class_name());
    }

    /**
     * @param int $offset
     *
     * @return Workspace[] | DataClassIterator
     */
    public function findWorkspaces($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            Workspace::class_name(), new DataClassRetrievesParameters(null, 1000, $offset)
        );
    }

    /**
     * Finds a content object by a given id
     *
     * @param int $contentObjectId
     *
     * @return ContentObject | DataClass
     */
    public function findContentObjectById($contentObjectId)
    {
        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $contentObjectId);
    }
}