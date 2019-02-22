<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPlagiarismResultRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * @var \Chamilo\Libraries\Storage\Query\FilterParametersTranslator
     */
    protected $filterParametersTranslator;

    /**
     * EntryPlagiarismResultRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Query\FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository,
        FilterParametersTranslator $filterParametersTranslator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult $contentObjectPlagiarismResult
     *
     * @return bool
     */
    public function createPlagiarismResult(ContentObjectPlagiarismResult $contentObjectPlagiarismResult)
    {
        return $this->dataClassRepository->create($contentObjectPlagiarismResult);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult $contentObjectPlagiarismResult
     *
     * @return bool
     */
    public function updatePlagiarismResult(ContentObjectPlagiarismResult $contentObjectPlagiarismResult)
    {
        return $this->dataClassRepository->update($contentObjectPlagiarismResult);
    }

    /**
     * @param int $plagiarismResultId
     *
     * @return ContentObjectPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlagiarismResultById(int $plagiarismResultId)
    {
        return $this->dataClassRepository->retrieveById(ContentObjectPlagiarismResult::class, $plagiarismResultId);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlagiarismResultByContentObject(Course $course, ContentObject $contentObject)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObject->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            ContentObjectPlagiarismResult::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param string $externalId
     *
     * @return ContentObjectPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findPlagiarismResultByExternalId(string $externalId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_EXTERNAL_ID
            ), new StaticConditionVariable($externalId)
        );

        return $this->dataClassRepository->retrieve(
            ContentObjectPlagiarismResult::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countPlagiarismResults(Course $course, FilterParameters $filterParameters)
    {
        $parameters = new DataClassCountParameters();
        $this->setPlagiarismResultParameters($parameters, $course, $filterParameters);

        return $this->dataClassRepository->count(ContentObjectPlagiarismResult::class, $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findPlagiarismResults(Course $course, FilterParameters $filterParameters = null)
    {
        $parameters = new RecordRetrievesParameters();
        $this->setPlagiarismResultParameters($parameters, $course, $filterParameters);

        $recordProperties = new DataClassProperties();

        $recordProperties->add(new PropertiesConditionVariable(ContentObjectPlagiarismResult::class));
        $recordProperties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE));

        $recordProperties->add(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );

        $recordProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $recordProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));

        $parameters->setDataClassProperties($recordProperties);

        return $this->dataClassRepository->records(ContentObjectPlagiarismResult::class, $parameters);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $dataClassParameters
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     */
    protected function setPlagiarismResultParameters(
        DataClassParameters $dataClassParameters, Course $course, FilterParameters $filterParameters = null
    )
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($course->getId())
        );

        $searchProperties = new DataClassProperties();
        $searchProperties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE));

        $searchProperties->add(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );

        $searchProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $searchProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));

        if(!empty($filterParameters))
        {
            $this->filterParametersTranslator->translateFilterParameters(
                $filterParameters, $searchProperties, $dataClassParameters, $condition
            );
        }

        $joins = new Joins();
        $joins->add(
            new Join(
                ContentObject::class,
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(
                        ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_CONTENT_OBJECT_ID
                    )
                )
            )
        );

        $joins->add(
            new Join(
                User::class,
                new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable(
                        ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_REQUEST_USER_ID
                    )
                )
            )
        );

        $dataClassParameters->setJoins($joins);
    }
}