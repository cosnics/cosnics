<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryRubricResult;
use Chamilo\Core\User\Storage\DataClass\User;
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
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository
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

    /*public function getUserEntity(string $contextClass, int $contextId, int $entityType, int $entityId)
    {
        $class_name = EvaluationEntry::class_name();

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable($contextClass)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_CONTEXT_ID),
            new StaticConditionVariable($contextId)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );

        $properties = new DataClassProperties([
            new FixedPropertyConditionVariable(EvaluationEntry::class_name(), User::PROPERTY_ID, 'entry_id'),
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE),
        ]);

        $userJoinConditions = array();
        $userJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_ENTITY_ID)
        );

        $joins = new Joins();
        $joins->add(new Join(User::class_name(), new AndCondition($userJoinConditions), Join::TYPE_LEFT));

        $parameters = new RecordRetrieveParameters($properties);
        $parameters->setCondition(new AndCondition($conditions));
        $parameters->setJoins($joins);

        return $this->dataClassRepository->record($class_name, $parameters);
    }*/

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
                new PropertyConditionVariable(EvaluationEntryFeedback::class_name(), EvaluationEntryFeedback::PROPERTY_ENTRY_ID),
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
    protected function getUserEntityDataClassProperties(): DataClassProperties
    {
        $class_name = User::class_name();
        return new DataClassProperties([
            new PropertyConditionVariable($class_name, User::PROPERTY_ID),
            new PropertyConditionVariable($class_name, User::PROPERTY_FIRSTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_LASTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_OFFICIAL_CODE)
        ]);
    }

    /**
     *
     * @param int[] $userIds
     * @param ContextIdentifier $contextIdentifier
     * @param FilterParameters $filterParameters
     *
     * @return RecordIterator
     */
    public function getUsersFromIds(array $userIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters): RecordIterator
    {
        $condition = new InCondition(new PropertyConditionVariable(User::class_name(), DataClass::PROPERTY_ID), $userIds);

        $searchProperties = $this->getUserEntityDataClassProperties();
        $retrieveProperties = $this->getRetrieveProperties($this->getUserEntityDataClassProperties());

        $joins = $this->getEvaluationEntryJoins(User::class, $contextIdentifier);

        $group_by = new GroupBy();
        $group_by->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID));

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $parameters->setJoins($joins);
        $parameters->setGroupBy($group_by);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->records(User::class_name(), $parameters);
    }

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countUsersFromIds(array $userIds, FilterParameters $filterParameters): int
    {
        $condition = new InCondition(new PropertyConditionVariable(User::class_name(), DataClass::PROPERTY_ID), $userIds);

        $retrieveProperties = $searchProperties = $this->getUserEntityDataClassProperties();
        $parameters = new DataClassCountParameters();
        $parameters->setDataClassProperties($retrieveProperties);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->count(User::class_name(), $parameters);
    }

    /**
     * @return DataClassProperties
     */
    protected function getPlatformGroupEntityDataClassProperties(): DataClassProperties
    {
        return new DataClassProperties([
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID),
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME)
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
    public function getPlatformGroupsFromIds(array $groupIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters): RecordIterator
    {
        $condition = new InCondition(new PropertyConditionVariable(Group::class_name(), DataClass::PROPERTY_ID), $groupIds);

        $searchProperties = $this->getPlatformGroupEntityDataClassProperties();
        $retrieveProperties = $this->getRetrieveProperties($this->getPlatformGroupEntityDataClassProperties());

        $joins = $this->getEvaluationEntryJoins(Group::class, $contextIdentifier);

        $group_by = new GroupBy();
        $group_by->add(new PropertyConditionVariable(Group::class_name(), User::PROPERTY_ID));

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $parameters->setJoins($joins);
        $parameters->setGroupBy($group_by);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->records(Group::class_name(), $parameters);
    }

    /**
     *
     * @param int[] $groupIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countPlatformGroupsFromIds(array $groupIds, FilterParameters $filterParameters): int
    {
        $condition = new InCondition(new PropertyConditionVariable(Group::class_name(), DataClass::PROPERTY_ID), $groupIds);

        $retrieveProperties = $searchProperties = $this->getPlatformGroupEntityDataClassProperties();
        $parameters = new DataClassCountParameters();
        $parameters->setDataClassProperties($retrieveProperties);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->count(Group::class_name(), $parameters);
    }

}