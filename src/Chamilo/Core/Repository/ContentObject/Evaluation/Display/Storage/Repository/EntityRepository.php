<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
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
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScoreTargetUser;
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

    /**
     * @return DataClassProperties
     */
    protected function getDataClassProperties(): DataClassProperties
    {
        $class_name = User::class_name();
        $properties = new DataClassProperties([
            new PropertyConditionVariable($class_name, User::PROPERTY_ID),
            new PropertyConditionVariable($class_name, User::PROPERTY_FIRSTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_LASTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_OFFICIAL_CODE)
        ]);
        return $properties;
    }

    /**
     *
     * @param int[] $userIds
     * @param ContextIdentifier $contextIdentifier
     * @param FilterParameters $filterParameters
     *
     * @return RecordIterator
     */
    public function getUsersFromIDs(array $userIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters): RecordIterator
    {
        $class_name = User::class_name();
        $condition = new InCondition(new PropertyConditionVariable($class_name, DataClass::PROPERTY_ID), $userIds);

        $searchProperties = $this->getDataClassProperties();

        $retrieveProperties = $this->getDataClassProperties()->get();
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

        $entryJoinConditions = array();
        $entryJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(EvaluationEntry::class_name(), EvaluationEntry::PROPERTY_ENTITY_ID),
            new PropertyConditionVariable(User::class_name(), USER::PROPERTY_ID)
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

        $group_by = new GroupBy();
        $group_by->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID));

        $joins = new Joins();
        $joins->add(new Join(EvaluationEntry::class_name(), new AndCondition($entryJoinConditions), Join::TYPE_LEFT));
        $joins->add(new Join(EvaluationEntryScore::class_name(), new AndCondition($scoreJoinConditions), Join::TYPE_LEFT));
        $joins->add(new Join(EvaluationEntryFeedback::class_name(), new AndCondition($feedbackJoinConditions), Join::TYPE_LEFT));

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $parameters->setJoins($joins);
        $parameters->setGroupBy($group_by);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->records($class_name, $parameters);
    }

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countUsersFromIDs(array $userIds, FilterParameters $filterParameters): int
    {
        $class_name = User::class_name();
        $condition = new InCondition(new PropertyConditionVariable($class_name, DataClass::PROPERTY_ID), $userIds);

        $retrieveProperties = $searchProperties = $this->getDataClassProperties();
        $parameters = new DataClassCountParameters();
        $parameters->setDataClassProperties($retrieveProperties);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->count($class_name, $parameters);
    }

    /**
     * @param ContextIdentifier $contextIdentifier
     * @param int $entityType
     * @param int $entityId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|DataClass|false
     */
    public function getEvaluationEntry(ContextIdentifier $contextIdentifier, int $entityType, int $entityId)
    {
        $class_name = EvaluationEntry::class_name();
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable($contextIdentifier->getContextClass())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_CONTEXT_ID),
            new StaticConditionVariable($contextIdentifier->getContextId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntry::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );
        $condition = new AndCondition($conditions);

        $parameters = new DataClassRetrieveParameters($condition);

        return $this->dataClassRepository->retrieve($class_name, $parameters);
    }

    /**
     * @param EvaluationEntry $entry
     *
     * @return bool
     */
    public function createEvaluationEntry(EvaluationEntry $entry): bool
    {
        return $this->dataClassRepository->create($entry);
    }

    /**
     * @param EvaluationEntry $entry
     *
     * @return bool
     */
    public function updateEvaluationEntry(EvaluationEntry $entry): bool
    {
        return $this->dataClassRepository->update($entry);
    }

    /**
     * @param int $entryId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|DataClass|false
     */
    public function getEvaluationEntryScore(int $entryId)
    {
        $class_name = EvaluationEntryScore::class_name();

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, EvaluationEntryScore::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entryId)
        );
        $condition = new AndCondition($conditions);
        $parameters = new DataClassRetrieveParameters($condition);

        return $this->dataClassRepository->retrieve($class_name, $parameters);
    }

    /**
     * @param EvaluationEntryScore $entryScore
     *
     * @return bool
     */
    public function createEvaluationEntryScore(EvaluationEntryScore $entryScore): bool
    {
        return $this->dataClassRepository->create($entryScore);
    }

    /**
     * @param EvaluationEntryScore $entryScore
     *
     * @return bool
     */
    public function updateEvaluationEntryScore(EvaluationEntryScore $entryScore): bool
    {
        return $this->dataClassRepository->update($entryScore);
    }

    /**
     * @param EvaluationEntryScoreTargetUser $targetUser
     *
     * @return bool
     */
    public function createEvaluationEntryScoreTargetUser(EvaluationEntryScoreTargetUser $targetUser): bool
    {
        return $this->dataClassRepository->create($targetUser);
    }

    /**
     * @param EvaluationEntryScoreTargetUser $targetUser
     *
     * @return bool
     */
    public function updateEvaluationEntryScoreTargetUser(EvaluationEntryScoreTargetUser $targetUser): bool
    {
        return $this->dataClassRepository->update($targetUser);
    }
}