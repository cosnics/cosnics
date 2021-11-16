<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntry;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScore;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\EvaluationEntryScoreTargetUser;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class EvaluationEntryRepository
{
    /**
     * @var DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
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