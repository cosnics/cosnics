<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Service;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RelationInstanceService
{
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_TARGET = 'target';

    /**
     *
     * @param string $encodedDataClassEntity
     *
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity
     */
    public function convertEncodedDataClassEntityValuesToDataClassEntity($encodedDataClassEntity)
    {
        $dataClassEntity = unserialize($encodedDataClassEntity);

        return $this->getService(DataClassEntityFactory::class)->getEntityFromDataClassNameAndDataClassIdentifier(
            $dataClassEntity[DataClassEntity::PROPERTY_TYPE], $dataClassEntity[DataClassEntity::PROPERTY_IDENTIFIER]
        );
    }

    /**
     *
     * @param User $user
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $sourceEntities
     * @param integer[] $relationIds
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity[] $targetEntities
     *
     * @return boolean
     * @throws \Exception
     */
    public function createRelationInstances(User $user, $sourceEntities, $relationIds, $targetEntities)
    {
        $failures = 0;
        $attempts = 0;

        foreach ($sourceEntities as $sourceEntity)
        {
            foreach ($relationIds as $relationId)
            {
                foreach ($targetEntities as $targetEntity)
                {
                    if (!$this->relationInstanceExists(
                        $sourceEntity->getDataClassName(), $sourceEntity->getDataClassIdentifier(),
                        $targetEntity->getDataClassName(), $targetEntity->getDataClassIdentifier(), $relationId
                    ))
                    {
                        $attempts ++;

                        $relationInstance = new RelationInstance();
                        $relationInstance->set_source_type($sourceEntity->getDataClassName());
                        $relationInstance->set_source_id($sourceEntity->getDataClassIdentifier());
                        $relationInstance->set_target_type($targetEntity->getDataClassName());
                        $relationInstance->set_target_id($targetEntity->getDataClassIdentifier());
                        $relationInstance->set_relation_id($relationId);
                        $relationInstance->set_user_id($user->get_id());
                        $relationInstance->set_creation_date(time());

                        if (!$relationInstance->create())
                        {
                            $failures ++;
                        }
                    }
                }
            }
        }

        return !($failures > 0);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string[][] $submittedValues
     *
     * @return boolean
     */
    public function createRelationInstancesFromSubmittedValues(User $user, $submittedValues)
    {
        $relationIds = $submittedValues[RelationInstance::PROPERTY_RELATION_ID];

        $sourceEntities = $this->getDataClassEntitiesFromTypeFromSubmittedValues(
            self::PROPERTY_SOURCE, $submittedValues
        );

        $targetEntities = $this->getDataClassEntitiesFromTypeFromSubmittedValues(
            self::PROPERTY_TARGET, $submittedValues
        );

        return $this->createRelationInstances($user, $sourceEntities, $relationIds, $targetEntities);
    }

    /**
     *
     * @param string $type
     * @param string[][] $submittedValues
     *
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntity[]
     */
    public function getDataClassEntitiesFromTypeFromSubmittedValues($type, $submittedValues)
    {
        $dataClassEntities = [];

        foreach ($submittedValues[$type] as $encodedDataClassEntity)
        {
            $dataClassEntities[] = $this->convertEncodedDataClassEntityValuesToDataClassEntity($encodedDataClassEntity);
        }

        return $dataClassEntities;
    }

    /**
     *
     * @param string $sourceType
     * @param integer $sourceIdentifier
     * @param string $targetType
     * @param integer $targetIdentifier
     * @param integer $relationId
     *
     * @return boolean
     */
    public function relationInstanceExists($sourceType, $sourceIdentifier, $targetType, $targetIdentifier, $relationId)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_TYPE),
            new StaticConditionVariable($sourceType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_ID),
            new StaticConditionVariable($sourceIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_TARGET_TYPE),
            new StaticConditionVariable($targetType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_TARGET_ID),
            new StaticConditionVariable($targetIdentifier)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_RELATION_ID),
            new StaticConditionVariable($relationId)
        );

        $condition = new AndCondition($conditions);

        return DataManager::count(RelationInstance::class, new DataClassCountParameters($condition)) > 0;
    }
}