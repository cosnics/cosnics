<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\ElementInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityService
{
    const PROPERTY_METADATA_SCHEMA = 'schema';
    const PROPERTY_METADATA_SCHEMA_EXISTING = 'existing';
    const PROPERTY_METADATA_SCHEMA_NEW = 'new';

    /**
     * @var \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    private $relationService;

    /**
     * @var \Chamilo\Core\Metadata\Element\Service\ElementService;
     */
    private $elementService;

    /**
     * @var \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    private $propertyProviderService;

    /**
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     */
    public function __construct(RelationService $relationService, ElementService $elementService)
    {
        $this->relationService = $relationService;
        $this->elementService = $elementService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return integer[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getAvailableSchemaIdsForEntityType(DataClassEntity $entity)
    {
        return $this->getSourceRelationIdsForEntity(
            Schema::class, $this->getRelationService()->getRelationByName('isAvailableFor'), $entity
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Schema[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getAvailableSchemasForEntityType(DataClassEntity $entity)
    {
        $schemaIds = $this->getAvailableSchemaIdsForEntityType($entity);

        return DataManager::retrieves(
            Schema::class, new DataClassRetrievesParameters(
                new InCondition(new PropertyConditionVariable(Schema::class, Schema::PROPERTY_ID), $schemaIds)
            )
        );
    }

    /**
     * @return \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    public function getElementService(): ElementService
    {
        return $this->elementService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     */
    public function setElementService(ElementService $elementService): void
    {
        $this->elementService = $elementService;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition[]
     */
    private function getEntityCondition(DataClassEntity $entity)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance::class, SchemaInstance::PROPERTY_ENTITY_TYPE),
            ComparisonCondition::EQUAL, new StaticConditionVariable($entity->getDataClassName())
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance::class, SchemaInstance::PROPERTY_ENTITY_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($entity->getDataClassIdentifier())
        );

        return $conditions;
    }

    /**
     * @return \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    public function getPropertyProviderService(): PropertyProviderService
    {
        return $this->propertyProviderService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService $propertyProviderService
     */
    public function setPropertyProviderService(
        PropertyProviderService $propertyProviderService
    ): void
    {
        $this->propertyProviderService = $propertyProviderService;
    }

    /**
     * @return \Chamilo\Core\Metadata\Relation\Service\RelationService
     */
    public function getRelationService(): RelationService
    {
        return $this->relationService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     */
    public function setRelationService(RelationService $relationService): void
    {
        $this->relationService = $relationService;
    }

    /**
     * @param $entity
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getSchemaInstancesForEntity($entity)
    {
        $schemaIds = $this->getAvailableSchemaIdsForEntityType($entity);

        $conditions = $this->getEntityCondition($entity);
        $conditions[] = new InCondition(
            new PropertyConditionVariable(SchemaInstance::class, SchemaInstance::PROPERTY_SCHEMA_ID), $schemaIds
        );

        return DataManager::retrieves(
            SchemaInstance::class, new DataClassRetrievesParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance[]
     * @throws \Exception
     */
    public function getSchemaInstancesForSchemaAndEntity(Schema $schema, DataClassEntity $entity)
    {
        $conditions = $this->getEntityCondition($entity);
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance::class, SchemaInstance::PROPERTY_SCHEMA_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($schema->get_id())
        );

        return DataManager::retrieves(
            SchemaInstance::class, new DataClassRetrievesParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param string $sourceType
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Relation $relation
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $targetEntity
     *
     * @return string[]
     * @throws \Exception
     */
    public function getSourceRelationIdsForEntity($sourceType, Relation $relation, DataClassEntity $targetEntity)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_TYPE),
            ComparisonCondition::EQUAL, new StaticConditionVariable($sourceType)
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_RELATION_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($relation->getId())
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_TARGET_TYPE),
            ComparisonCondition::EQUAL, new StaticConditionVariable($targetEntity->getDataClassName())
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_TARGET_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($targetEntity->getDataClassIdentifier())
        );

        $condition = new AndCondition($conditions);

        return DataManager::distinct(
            RelationInstance::class, new DataClassDistinctParameters(
                $condition, new DataClassProperties(
                    array(new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_ID))
                )
            )
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary[]
     * @throws \Exception
     */
    public function getVocabularyByElementIdAndUserId(Element $element, User $user)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ELEMENT_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($element->getId())
        );

        return DataManager::retrieves(
            Vocabulary::class, new DataClassRetrievesParameters(new AndCondition($conditions))
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param $submittedElementValues
     *
     * @return bool
     * @throws \Exception
     */
    private function processEntityElement(
        User $currentUser, SchemaInstance $schemaInstance, Element $element, DataClassEntity $entity,
        $submittedElementValues
    )
    {
        try
        {
            $providerLink = $this->getPropertyProviderService()->getProviderLinkForElement($entity, $element);

            return true;
        }
        catch (NoProviderAvailableException $exception)
        {
            if ($element->usesVocabulary())
            {
                return $this->processEntityVocabularyElement(
                    $currentUser, $schemaInstance, $element, $submittedElementValues
                );
            }
            else
            {
                return $this->processEntityFreeElement(
                    $currentUser, $schemaInstance, $element, $submittedElementValues
                );
            }
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     * @param $submittedElementValue
     *
     * @return bool
     * @throws \Exception
     */
    private function processEntityFreeElement(
        User $currentUser, SchemaInstance $schemaInstance, Element $element, $submittedElementValue
    )
    {
        $existingElementInstance = $this->getElementService()->getElementInstanceForSchemaInstanceAndElement(
            $schemaInstance, $element
        );

        if ($existingElementInstance instanceof ElementInstance)
        {
            $vocabulary = $existingElementInstance->getVocabulary();
            $vocabulary->set_value($submittedElementValue);

            if (!$vocabulary->update())
            {
                return false;
            }
        }
        else
        {
            $vocabulary = new Vocabulary();
            $vocabulary->set_element_id($element->getId());
            $vocabulary->set_user_id($currentUser->getId());
            $vocabulary->set_value($submittedElementValue);

            if (!$vocabulary->create())
            {
                return false;
            }

            $elementInstance = new ElementInstance();
            $elementInstance->set_schema_instance_id($schemaInstance->getId());
            $elementInstance->set_element_id($element->getId());
            $elementInstance->set_vocabulary_id($vocabulary->getId());

            if (!$elementInstance->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param $submittedSchemaValues
     *
     * @return bool
     * @throws \Exception
     */
    private function processEntitySchema(
        User $currentUser, Schema $schema, DataClassEntity $entity, $submittedSchemaValues
    )
    {
        $existingSchemaInstances = $this->getSchemaInstancesForSchemaAndEntity($schema, $entity)->as_array();
        $existingSchemaInstanceIds = array();

        foreach ($existingSchemaInstances as $existingSchemaInstance)
        {
            $existingSchemaInstanceIds[] = $existingSchemaInstance->get_id();
        }

        $submittedSchemaInstanceIds = array_keys($submittedSchemaValues);
        $submittedExistingSchemaIds = array_intersect($existingSchemaInstanceIds, $submittedSchemaInstanceIds);

        foreach ($submittedExistingSchemaIds as $submittedExistingSchemaId)
        {
            $schemaInstance = DataManager::retrieve_by_id(SchemaInstance::class, $submittedExistingSchemaId);

            if (!$this->processEntitySchemaInstance(
                $currentUser, $schemaInstance, $entity, $submittedSchemaValues[$submittedExistingSchemaId]
            ))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param $submittedSchemaInstanceValues
     *
     * @return bool
     * @throws \Exception
     */
    private function processEntitySchemaInstance(
        User $currentUser, SchemaInstance $schemaInstance, DataClassEntity $entity, $submittedSchemaInstanceValues
    )
    {
        $elements = $this->getElementService()->getElementsForSchemaInstance($schemaInstance);

        while ($element = $elements->next_result())
        {
            if (!$this->processEntityElement(
                $currentUser, $schemaInstance, $element, $entity, $submittedSchemaInstanceValues[$element->get_id()]
            ))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     * @param $submittedElementValues
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function processEntityVocabularyElement(
        User $currentUser, SchemaInstance $schemaInstance, Element $element, $submittedElementValues
    )
    {
        $existingElementInstances = $this->getElementService()->getElementInstancesForSchemaInstanceAndElement(
            $schemaInstance, $element
        )->as_array();
        $existingElementInstanceIds = array();

        foreach ($existingElementInstances as $existingElementInstance)
        {
            $existingElementInstanceIds[] = $existingElementInstance->get_id();
        }

        $submittedExistingElementInstanceIds =
            $submittedElementValues[EntityService::PROPERTY_METADATA_SCHEMA_EXISTING];
        $submittedExistingElementInstanceIds = $submittedExistingElementInstanceIds ? explode(
            ',', $submittedExistingElementInstanceIds
        ) : array();

        if ($element->isVocabularyUserDefined())
        {
            $submittedNewElementInstanceValues = $submittedElementValues[EntityService::PROPERTY_METADATA_SCHEMA_NEW];
            $submittedNewElementInstanceValues = $submittedNewElementInstanceValues ? explode(
                ',', $submittedNewElementInstanceValues
            ) : array();

            $totalValues = count($submittedExistingElementInstanceIds) + count($submittedNewElementInstanceValues);
        }
        else
        {
            $totalValues = count($submittedExistingElementInstanceIds);
        }

        if ($element->isNumberOfValuesLimited() && $totalValues > $element->get_value_limit())
        {
            return false;
        }

        $elementInstanceIdsToDelete = array_diff($existingElementInstanceIds, $submittedExistingElementInstanceIds);
        $elementInstanceIdsToAdd = array_diff($submittedExistingElementInstanceIds, $existingElementInstanceIds);

        foreach ($elementInstanceIdsToDelete as $elementInstanceIdToDelete)
        {
            $elementInstance = DataManager::retrieve_by_id(ElementInstance::class, $elementInstanceIdToDelete);

            if (!$elementInstance->delete())
            {
                return false;
            }
        }

        if ($element->isVocabularyUserDefined())
        {
            foreach ($submittedNewElementInstanceValues as $submittedNewElementInstanceValue)
            {
                $vocabulary = new Vocabulary();
                $vocabulary->set_element_id($element->getId());
                $vocabulary->set_user_id($currentUser->getId());
                $vocabulary->set_value($submittedNewElementInstanceValue);

                if (!$vocabulary->create())
                {
                    return false;
                }
                else
                {
                    $elementInstanceIdsToAdd[] = $vocabulary->getId();
                }
            }
        }

        foreach ($elementInstanceIdsToAdd as $elementInstanceIdToAdd)
        {
            $elementInstance = new ElementInstance();
            $elementInstance->set_schema_instance_id($schemaInstance->getId());
            $elementInstance->set_element_id($element->getId());
            $elementInstance->set_vocabulary_id($elementInstanceIdToAdd);

            if (!$elementInstance->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param $submittedSchemaValues
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function updateEntitySchemaValues(
        User $currentUser, DataClassEntity $entity, $submittedSchemaValues
    )
    {
        $availableSchemaIdsForEntity = $this->getAvailableSchemaIdsForEntityType($entity);

        $submittedSchemaIds = array_keys($submittedSchemaValues);

        $submittedAvailableSchemaIds = array_intersect($submittedSchemaIds, $availableSchemaIdsForEntity);

        foreach ($submittedAvailableSchemaIds as $submittedAvailableSchemaId)
        {
            $schema = DataManager::retrieve_by_id(Schema::class, $submittedAvailableSchemaId);
            if (!$this->processEntitySchema(
                $currentUser, $schema, $entity, $submittedSchemaValues[$submittedAvailableSchemaId]
            ))
            {
                return false;
            }
        }

        return true;
    }
}
