<?php

namespace Chamilo\Application\Weblcms\Bridge\Evaluation\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryRubricResult;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryFeedback;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

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
     * @var FilterParametersTranslator
     */
    private $filterParametersTranslator;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Query\FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, FilterParametersTranslator $filterParametersTranslator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    protected function getRetrieveProperties(DataClassProperties $properties): DataClassProperties
    {
        $retrieveProperties = $properties->get();
        $retrieveProperties[] = new FixedPropertyConditionVariable(EvaluationEntryScore::class_name(), EvaluationEntryScore::PROPERTY_SCORE, 'score');
        $retrieveProperties[] = new FixedPropertyConditionVariable(EvaluationEntryScore::class_name(), EvaluationEntryScore::PROPERTY_IS_ABSENT, 'is_absent');
        $retrieveProperties = new DataClassProperties($retrieveProperties);

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT,
                    new PropertyConditionVariable(EvaluationEntryScore::class_name(), EvaluationEntryScore::PROPERTY_ENTRY_ID)
                ),
                'score_registered'
            )
        );

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT,
                    new PropertyConditionVariable(EvaluationEntryFeedback::class_name(), EvaluationEntryFeedback::PROPERTY_ID)
                ),
                'feedback_count'
            )
        );

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new FunctionConditionVariable(
                    FunctionConditionVariable::DISTINCT,
                    new PropertyConditionVariable(EvaluationEntryRubricResult::class_name(), EvaluationEntryRubricResult::PROPERTY_CONTEXT_CLASS)
                ),
                'rubric'
            )
        );

        return $retrieveProperties;
    }

    /**
     * @param $class
     * @param ContextIdentifier $contextIdentifier
     * @return Joins
     */
    protected function getEvaluationEntryJoins($class, ContextIdentifier $contextIdentifier): Joins
    {
        $entryJoinConditions = array();
        $entryJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_ENTITY_ID),
            new PropertyConditionVariable($class::class_name(), $class::PROPERTY_ID)
        );
        $entryJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable($contextIdentifier->getContextClass())
        );
        $entryJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_CONTEXT_ID),
            new StaticConditionVariable($contextIdentifier->getContextId())
        );

        $feedbackJoinConditions = array();
        $feedbackJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntryFeedback::class_name(), EvaluationEntryFeedback::PROPERTY_ENTRY_ID),
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_ID)
        );

        $scoreJoinConditions = array();
        $scoreJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntryScore::class_name(), EvaluationEntryScore::PROPERTY_ENTRY_ID),
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_ID)
        );

        $rubricJoinConditions = array();
        $rubricJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntryRubricResult::class_name(), EvaluationEntryRubricResult::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable(EvaluationEntry::class_name())
        );
        $rubricJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntryRubricResult::class_name(), EvaluationEntryRubricResult::PROPERTY_CONTEXT_ID),
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_ID)
        );

        $joins = new Joins();
        $joins->add(new Join(EvaluationEntry::class_name(), new AndCondition($entryJoinConditions), Join::TYPE_LEFT));
        $joins->add(new Join(EvaluationEntryScore::class_name(), new AndCondition($scoreJoinConditions), Join::TYPE_LEFT));
        $joins->add(new Join(EvaluationEntryFeedback::class_name(), new AndCondition($feedbackJoinConditions), Join::TYPE_LEFT));
        $joins->add(new Join(EvaluationEntryRubricResult::class_name(), new AndCondition($rubricJoinConditions), Join::TYPE_LEFT));
        return $joins;
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
     * @param FilterParameters $filterParameters
     *
     * @return RecordIterator
     */
    public function getGroupsFromIds(array $groupIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters): RecordIterator
    {
        $condition = new InCondition(new PropertyConditionVariable(CourseGroup::class_name(), DataClass::PROPERTY_ID), $groupIds);

        $searchProperties = $this->getCourseGroupEntityDataClassProperties();
        $retrieveProperties = $this->getRetrieveProperties($this->getCourseGroupEntityDataClassProperties());

        $joins = $this->getEvaluationEntryJoins(CourseGroup::class, $contextIdentifier);

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