<?php

namespace Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
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
 * @package Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolResultRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * @var \Chamilo\Libraries\Storage\Query\FilterParametersTranslator
     */
    protected $filterParametersTranslator;

    /**
     * TreeNodeDataRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Query\FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, FilterParametersTranslator $filterParametersTranslator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    /**
     * @param int $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findById(int $id)
    {
        return $this->dataClassRepository->retrieveById(ExternalToolResult::class, $id);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|ExternalToolResult
     */
    public function findByContentObjectPublicationAndUser(
        ContentObjectPublication $contentObjectPublication, User $user
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ExternalToolResult::class, ExternalToolResult::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ), new StaticConditionVariable($contentObjectPublication->getId())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ExternalToolResult::class, ExternalToolResult::PROPERTY_USER_ID
            ), new StaticConditionVariable($user->getId())
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            ExternalToolResult::class, new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult $externalTool
     *
     * @return bool
     */
    public function create(ExternalToolResult $externalTool)
    {
        return $this->dataClassRepository->create($externalTool);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult $externalTool
     *
     * @return bool
     */
    public function update(ExternalToolResult $externalTool)
    {
        return $this->dataClassRepository->update($externalTool);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult $externalTool
     *
     * @return bool
     */
    public function delete(ExternalToolResult $externalTool)
    {
        return $this->dataClassRepository->delete($externalTool);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     * @throws \Exception
     */
    public function getResultsWithUsers(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $retrieveProperties = new DataClassProperties();
        $retrieveProperties->add(new PropertiesConditionVariable(ExternalToolResult::class));
        $retrieveProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $retrieveProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));

        $parameters = new RecordRetrievesParameters();
        $parameters->setDataClassProperties($retrieveProperties);

        $this->prepareParametersForResultsWithUsers(
            $contentObjectPublication, $filterParameters, $parameters
        );

        return $this->dataClassRepository->records(User::class, $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countResultsWithUsers(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $parameters = new DataClassCountParameters();

        $this->prepareParametersForResultsWithUsers(
            $contentObjectPublication, $filterParameters, $parameters
        );

        return $this->dataClassRepository->count(User::class, $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassParameters $dataClassParameters
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassParameters
     */
    protected function prepareParametersForResultsWithUsers(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters,
        DataClassParameters $dataClassParameters
    )
    {
        $contextCondition = new EqualityCondition(
            new PropertyConditionVariable(
                ExternalToolResult::class, ExternalToolResult::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ), new StaticConditionVariable($contentObjectPublication->getId())
        );

        $searchProperties = new DataClassProperties();
        $searchProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $searchProperties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));

        $joins = new Joins();
        $joins->add(
            new Join(
                ExternalToolResult::class,
                new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable(ExternalToolResult::class, ExternalToolResult::PROPERTY_USER_ID)
                )
            )
        );

        $dataClassParameters->setJoins($joins);

        $this->filterParametersTranslator->translateFilterParameters(
            $filterParameters, $searchProperties, $dataClassParameters, $contextCondition
        );

        return $dataClassParameters;
    }
}