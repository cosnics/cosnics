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
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * Repository to retrieve the data to fix the content object resources
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResourceFixerRepository
{

    /**
     *
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
     *
     * @return int
     */
    public function countAssessmentMatchNumericQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatchNumericQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countAssessmentMatchTextQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatchTextQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countAssessmentMatchingQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatchingQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countAssessmentMatrixQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMatrixQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countAssessmentMultipleChoiceQuestions()
    {
        return $this->dataClassRepository->count(AssessmentMultipleChoiceQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countAssessmentRatingQuestions()
    {
        return $this->dataClassRepository->count(AssessmentRatingQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countAssessmentSelectQuestions()
    {
        return $this->dataClassRepository->count(AssessmentSelectQuestion::class);
    }

    /**
     * Counts the number of resource tags with a resource tag in their description field
     *
     * @return int
     */
    public function countContentObjectsWithResourceTags()
    {
        $conditions = [
            new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION), '<resource'
            ),
            new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION), 'data-co-id'
            )
        ];

        $parameters = new DataClassCountParameters(condition: new OrCondition($conditions));

        return $this->dataClassRepository->count(ContentObject::class, $parameters);
    }

    /**
     *
     * @return int
     */
    public function countFillInBlanksQuestions()
    {
        return $this->dataClassRepository->count(FillInBlanksQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countForumPosts()
    {
        return $this->dataClassRepository->count(ForumPost::class);
    }

    /**
     *
     * @return int
     */
    public function countHotspotQuestions()
    {
        return $this->dataClassRepository->count(HotspotQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countOrderingQuestions()
    {
        return $this->dataClassRepository->count(OrderingQuestion::class);
    }

    /**
     *
     * @return int
     */
    public function countWorkspaces()
    {
        return $this->dataClassRepository->count(Workspace::class);
    }

    /**
     *
     * @param int $offset
     *
     * @return AssessmentMatchNumericQuestion[] | ArrayCollection
     */
    public function findAssessmentMatchNumericQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatchNumericQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return AssessmentMatchTextQuestion[] | ArrayCollection
     */
    public function findAssessmentMatchTextQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatchTextQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return AssessmentMatchingQuestion[] | ArrayCollection
     */
    public function findAssessmentMatchingQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatchingQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return AssessmentMatrixQuestion[] | ArrayCollection
     */
    public function findAssessmentMatrixQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMatrixQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return AssessmentMultipleChoiceQuestion[] | ArrayCollection
     */
    public function findAssessmentMultipleChoiceQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentMultipleChoiceQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return AssessmentRatingQuestion[] | ArrayCollection
     */
    public function findAssessmentRatingQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentRatingQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return AssessmentSelectQuestion[] | ArrayCollection
     */
    public function findAssessmentSelectQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            AssessmentSelectQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
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
        return DataManager::retrieve_by_id(
            ContentObject::class, $contentObjectId
        );
    }

    /**
     * Returns the list of content objects with a resource tag in their description field
     *
     * @param int $offset
     *
     * @return ContentObject[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function findContentObjectsWithResourceTags($offset = 0)
    {
        $conditions = [
            new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION), '<resource'
            ),
            new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION), 'data-co-id'
            )
        ];

        $parameters = new RetrievesParameters(condition: new OrCondition($conditions), count: 1000, offset: $offset);

        return $this->dataClassRepository->retrieves(ContentObject::class, $parameters);
    }

    /**
     *
     * @param int $offset
     *
     * @return FillInBlanksQuestion[] | ArrayCollection
     */
    public function findFillInBlanksQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            FillInBlanksQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return ForumPost[] | ArrayCollection
     */
    public function findForumPosts($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            ForumPost::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return HotspotQuestion[] | ArrayCollection
     */
    public function findHotspotQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            HotspotQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return OrderingQuestion[] | ArrayCollection
     */
    public function findOrderingQuestions($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            OrderingQuestion::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }

    /**
     *
     * @param int $offset
     *
     * @return Workspace[] | ArrayCollection
     */
    public function findWorkspaces($offset = 0)
    {
        return $this->dataClassRepository->retrieves(
            Workspace::class, new RetrievesParameters(count: 1000, offset: $offset)
        );
    }
}