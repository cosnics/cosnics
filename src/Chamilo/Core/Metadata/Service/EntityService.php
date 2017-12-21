<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
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
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

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
    const PROPERTY_METADATA_SCHEMA_NEW = 'new';
    const PROPERTY_METADATA_SCHEMA_EXISTING = 'existing';

    /**
     *
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @return Schema[]
     */
    public function getAvailableSchemasForEntityType(RelationService $relationService, DataClassEntity $entity)
    {
        $schemaIds = $this->getAvailableSchemaIdsForEntityType($relationService, $entity);

        return DataManager::retrieves(
            Schema::class_name(),
            new DataClassRetrievesParameters(
                new InCondition(new PropertyConditionVariable(Schema::class_name(), Schema::PROPERTY_ID), $schemaIds)));
    }

    /**
     *
     * @param EntityService $entityService
     * @param RelationService $relationService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @return integer[]
     */
    public function getAvailableSchemaIdsForEntityType(RelationService $relationService, DataClassEntity $entity)
    {
        return $this->getSourceRelationIdsForEntity(
            Schema::class_name(),
            $relationService->getRelationByName('isAvailableFor'),
            $entity);
    }

    /**
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     * @param $entity
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getSchemaInstancesForEntity(RelationService $relationService, $entity)
    {
        $entityType = DataClassEntityFactory::getInstance()->getEntity($entity->getDataClassName());
        $schemaIds = $this->getAvailableSchemaIdsForEntityType($relationService, $entityType);

        $conditions = $this->getEntityCondition($entity);
        $conditions[] = new InCondition(
            new PropertyConditionVariable(SchemaInstance::class_name(), SchemaInstance::PROPERTY_SCHEMA_ID),
            $schemaIds);

        return DataManager::retrieves(
            SchemaInstance::class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));
    }

    /**
     *
     * @param Schema $schema
     * @param RelationService $relationService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @return \libraries\storage\ResultSet
     */
    public function getSchemaInstancesForSchemaAndEntity(Schema $schema, RelationService $relationService,
        DataClassEntity $entity)
    {
        $conditions = $this->getEntityCondition($entity);
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance::class_name(), SchemaInstance::PROPERTY_SCHEMA_ID),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($schema->get_id()));

        return DataManager::retrieves(
            SchemaInstance::class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @return \Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition[]
     */
    private function getEntityCondition(DataClassEntity $entity)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance::class_name(), SchemaInstance::PROPERTY_ENTITY_TYPE),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($entity->getDataClassName()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance::class_name(), SchemaInstance::PROPERTY_ENTITY_ID),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($entity->getDataClassIdentifier()));

        return $conditions;
    }

    /**
     *
     * @param string $sourceType
     * @param \Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation $relation
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $targetEntity
     * @return integer[]
     */
    public function getSourceRelationIdsForEntity($sourceType, Relation $relation, DataClassEntity $targetEntity)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_SOURCE_TYPE),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($sourceType));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_RELATION_ID),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($relation->get_id()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_TARGET_TYPE),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($targetEntity->getDataClassName()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance::class_name(), RelationInstance::PROPERTY_TARGET_ID),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($targetEntity->getDataClassIdentifier()));

        $condition = new AndCondition($conditions);

        return DataManager::distinct(
            RelationInstance::class_name(),
            new DataClassDistinctParameters(
                $condition,
                new DataClassProperties(
                    array(new PropertyConditionVariable(RelationInstance::class, RelationInstance::PROPERTY_SOURCE_ID)))));
    }

    /**
     *
     * @param Element $element
     * @param User $user
     */
    public function getVocabularyByElementIdAndUserId(Element $element, User $user)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Vocabulary::class_name(), Vocabulary::PROPERTY_ELEMENT_ID),
            ComparisonCondition::EQUAL,
            new StaticConditionVariable($element->get_id()));

        return DataManager::retrieves(
            Vocabulary::class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));
    }

    /**
     *
     * @param User $currentUser
     * @param RelationService $relationService
     * @param ElementService $elementService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string[] $submittedSchemaValues
     * @return boolean
     */
    public function updateEntitySchemaValues(User $currentUser, RelationService $relationService,
        ElementService $elementService, DataClassEntity $entity, $submittedSchemaValues)
    {
        $entityType = DataClassEntityFactory::getInstance()->getEntity($entity->getDataClassName());
        $availableSchemaIdsForEntity = $this->getAvailableSchemaIdsForEntityType($relationService, $entityType);

        $submittedSchemaIds = array_keys($submittedSchemaValues);

        $submittedAvailableSchemaIds = array_intersect($submittedSchemaIds, $availableSchemaIdsForEntity);

        foreach ($submittedAvailableSchemaIds as $submittedAvailableSchemaId)
        {
            $schema = DataManager::retrieve_by_id(Schema::class_name(), $submittedAvailableSchemaId);
            if (! $this->processEntitySchema(
                $currentUser,
                $schema,
                $relationService,
                $elementService,
                $entity,
                $submittedSchemaValues[$submittedAvailableSchemaId]))
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param User $currentUser
     * @param Schema $schema
     * @param RelationService $relationService
     * @param ElementService $elementService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string[] $submittedSchemaValues
     * @return boolean
     */
    private function processEntitySchema(User $currentUser, Schema $schema, RelationService $relationService,
        ElementService $elementService, DataClassEntity $entity, $submittedSchemaValues)
    {
        $existingSchemaInstances = $this->getSchemaInstancesForSchemaAndEntity($schema, $relationService, $entity)->as_array();
        $existingSchemaInstanceIds = array();

        foreach ($existingSchemaInstances as $existingSchemaInstance)
        {
            $existingSchemaInstanceIds[] = $existingSchemaInstance->get_id();
        }

        $submittedSchemaInstanceIds = array_keys($submittedSchemaValues);
        $submittedExistingSchemaIds = array_intersect($existingSchemaInstanceIds, $submittedSchemaInstanceIds);

        foreach ($submittedExistingSchemaIds as $submittedExistingSchemaId)
        {
            $schemaInstance = DataManager::retrieve_by_id(SchemaInstance::class_name(), $submittedExistingSchemaId);
            if (! $this->processEntitySchemaInstance(
                $currentUser,
                $schemaInstance,
                $elementService,
                $entity,
                $submittedSchemaValues[$submittedExistingSchemaId]))
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param User $currentUser
     * @param SchemaInstance $schemaInstance
     * @param ElementService $elementService
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string[] $submittedSchemaInstanceValues
     * @return boolean
     */
    private function processEntitySchemaInstance(User $currentUser, SchemaInstance $schemaInstance,
        ElementService $elementService, DataClassEntity $entity, $submittedSchemaInstanceValues)
    {
        $elements = $elementService->getElementsForSchemaInstance($schemaInstance);

        while ($element = $elements->next_result())
        {
            if (! $this->processEntityElement(
                $currentUser,
                $elementService,
                $schemaInstance,
                $element,
                $entity,
                $submittedSchemaInstanceValues[$element->get_id()]))
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param User $currentUser
     * @param ElementService $elementService
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param string[] $submittedElementValues
     * @return boolean
     */
    private function processEntityElement(User $currentUser, ElementService $elementService,
        SchemaInstance $schemaInstance, Element $element, DataClassEntity $entity, $submittedElementValues)
    {
        $propertyProviderService = new PropertyProviderService($entity);

        try
        {
            $providerLink = $propertyProviderService->getProviderLinkForElement($element);
            return true;
        }
        catch (NoProviderAvailableException $exception)
        {
            if ($element->usesVocabulary())
            {
                return $this->processEntityVocabularyElement(
                    $currentUser,
                    $elementService,
                    $schemaInstance,
                    $element,
                    $submittedElementValues);
            }
            else
            {
                return $this->processEntityFreeElement(
                    $currentUser,
                    $elementService,
                    $schemaInstance,
                    $element,
                    $submittedElementValues);
            }
        }
    }

    /**
     *
     * @param User $currentUser
     * @param ElementService $elementService
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @param string[] $submittedElementValue
     * @return boolean
     */
    private function processEntityFreeElement(User $currentUser, ElementService $elementService,
        SchemaInstance $schemaInstance, Element $element, $submittedElementValue)
    {
        $existingElementInstance = $elementService->getElementInstanceForSchemaInstanceAndElement(
            $schemaInstance,
            $element);

        if ($existingElementInstance instanceof ElementInstance)
        {
            $vocabulary = $existingElementInstance->getVocabulary();
            $vocabulary->set_value($submittedElementValue);

            if (! $vocabulary->update())
            {
                return false;
            }
        }
        else
        {
            $vocabulary = new Vocabulary();
            $vocabulary->set_element_id($element->get_id());
            $vocabulary->set_user_id($currentUser->get_id());
            $vocabulary->set_value($submittedElementValue);

            if (! $vocabulary->create())
            {
                return false;
            }

            $elementInstance = new ElementInstance();
            $elementInstance->set_schema_instance_id($schemaInstance->get_id());
            $elementInstance->set_element_id($element->get_id());
            $elementInstance->set_vocabulary_id($vocabulary->get_id());

            if (! $elementInstance->create())
            {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param User $currentUser
     * @param ElementService $elementService
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @param string[] $submittedElementValues
     * @return boolean
     */
    private function processEntityVocabularyElement(User $currentUser, ElementService $elementService,
        SchemaInstance $schemaInstance, Element $element, $submittedElementValues)
    {
        $existingElementInstances = $elementService->getElementInstancesForSchemaInstanceAndElement(
            $schemaInstance,
            $element)->as_array();
        $existingElementInstanceIds = array();

        foreach ($existingElementInstances as $existingElementInstance)
        {
            $existingElementInstanceIds[] = $existingElementInstance->get_id();
        }

        $submittedExistingElementInstanceIds = $submittedElementValues[EntityService::PROPERTY_METADATA_SCHEMA_EXISTING];
        $submittedExistingElementInstanceIds = $submittedExistingElementInstanceIds ? explode(
            ',',
            $submittedExistingElementInstanceIds) : array();

        if ($element->isVocabularyUserDefined())
        {
            $submittedNewElementInstanceValues = $submittedElementValues[EntityService::PROPERTY_METADATA_SCHEMA_NEW];
            $submittedNewElementInstanceValues = $submittedNewElementInstanceValues ? explode(
                ',',
                $submittedNewElementInstanceValues) : array();

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
            $elementInstance = DataManager::retrieve_by_id(ElementInstance::class_name(), $elementInstanceIdToDelete);

            if (! $elementInstance->delete())
            {
                return false;
            }
        }

        if ($element->isVocabularyUserDefined())
        {
            foreach ($submittedNewElementInstanceValues as $submittedNewElementInstanceValue)
            {
                $vocabulary = new Vocabulary();
                $vocabulary->set_element_id($element->get_id());
                $vocabulary->set_user_id($currentUser->get_id());
                $vocabulary->set_value($submittedNewElementInstanceValue);

                if (! $vocabulary->create())
                {
                    return false;
                }
                else
                {
                    $elementInstanceIdsToAdd[] = $vocabulary->get_id();
                }
            }
        }

        foreach ($elementInstanceIdsToAdd as $elementInstanceIdToAdd)
        {
            $elementInstance = new ElementInstance();
            $elementInstance->set_schema_instance_id($schemaInstance->get_id());
            $elementInstance->set_element_id($element->get_id());
            $elementInstance->set_vocabulary_id($elementInstanceIdToAdd);

            if (! $elementInstance->create())
            {
                return false;
            }
        }

        return true;
    }
}
