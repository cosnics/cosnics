<?php

namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Service\Entity\EvaluationEntityRetrieveProperties;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository\EntityRepository as MainEntityRepository;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Evaluation\Storage\Repository
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EntityRepository
{
    /**
     * @var DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @var MainEntityRepository
     */
    private $mainEntityRepository;

    /**
     * @var FilterParametersTranslator
     */
    private $filterParametersTranslator;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param MainEntityRepository $mainEntityRepository
     * @param \Chamilo\Libraries\Storage\Query\FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, MainEntityRepository $mainEntityRepository, FilterParametersTranslator $filterParametersTranslator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->mainEntityRepository = $mainEntityRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    /**
     * @return DataClassProperties
     */
    protected function getCourseGroupEntityDataClassProperties(): DataClassProperties
    {
        return new DataClassProperties([
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_ID),
            new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME)
        ]);
    }

    /**
     *
     * @param int[] $groupIds
     * @param ContextIdentifier $contextIdentifier
     * @param EvaluationEntityRetrieveProperties $evaluationEntityRetrieveProperties
     * @param FilterParameters $filterParameters
     *
     * @return RecordIterator
     */
    public function getGroupsFromIds(array $groupIds, ContextIdentifier $contextIdentifier, EvaluationEntityRetrieveProperties $evaluationEntityRetrieveProperties, FilterParameters $filterParameters): RecordIterator
    {
        $condition = new InCondition(new PropertyConditionVariable(CourseGroup::class_name(), DataClass::PROPERTY_ID), $groupIds);

        $searchProperties = $this->getCourseGroupEntityDataClassProperties();
        $retrieveProperties = $this->mainEntityRepository->getRetrieveProperties($this->getCourseGroupEntityDataClassProperties(), $evaluationEntityRetrieveProperties);

        $joins = $this->mainEntityRepository->getEvaluationEntryJoins(CourseGroup::class, $contextIdentifier, $evaluationEntityRetrieveProperties);

        $group_by = new GroupBy();
        $group_by->add(new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_ID));

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $parameters->setJoins($joins);
        $parameters->setGroupBy($group_by);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->records(CourseGroup::class_name(), $parameters);
    }

    /**
     *
     * @param int[] $groupIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countGroupsFromIds(array $groupIds, FilterParameters $filterParameters): int
    {
        $condition = new InCondition(new PropertyConditionVariable(CourseGroup::class_name(), DataClass::PROPERTY_ID), $groupIds);

        $retrieveProperties = $searchProperties = $this->getCourseGroupEntityDataClassProperties();
        $parameters = new DataClassCountParameters();
        $parameters->setDataClassProperties($retrieveProperties);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->count(CourseGroup::class_name(), $parameters);
    }
}