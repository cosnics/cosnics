<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\Metadata\Relation\Instance\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Element\Instance\Storage\DataClass\ElementInstance;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;

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
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     * @return integer[]
     */
    public function getAvailableSchemasForEntity(RelationService $relationService, DataClass $entity)
    {
        $schemaIds = $this->getAvailableSchemaIdsForEntity($relationService, $entity);

        return DataManager :: retrieves(
            Schema :: class_name(),
            new DataClassRetrievesParameters(
                new InCondition(new PropertyConditionVariable(Schema :: class_name(), Schema :: PROPERTY_ID), $schemaIds)));
    }

    /**
     *
     * @param EntityService $entityService
     * @param RelationService $relationService
     * @param DataClass $entity
     * @return integer[]
     */
    public function getAvailableSchemaIdsForEntity(RelationService $relationService, DataClass $entity)
    {
        return $this->getSourceRelationIdsForEntity(
            Schema :: class_name(),
            $relationService->getRelationByName('isAvailableFor'),
            $entity);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Relation\Service\RelationService $relationService
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     * @return integer[]
     */
    public function getSchemaInstancesForEntity(RelationService $relationService, DataClass $entity)
    {
        $schemaIds = $this->getAvailableSchemaIdsForEntity($relationService, $entity);

        $conditions = $this->getEntityCondition($entity);
        $conditions[] = new InCondition(
            new PropertyConditionVariable(SchemaInstance :: class_name(), SchemaInstance :: PROPERTY_SCHEMA_ID),
            $schemaIds);

        return DataManager :: retrieves(
            SchemaInstance :: class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));
    }

    public function getSchemaInstancesForSchemaAndEntity(Schema $schema, RelationService $relationService,
        DataClass $entity)
    {
        $conditions = $this->getEntityCondition($entity);
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance :: class_name(), SchemaInstance :: PROPERTY_SCHEMA_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($schema->get_id()));

        return DataManager :: retrieves(
            SchemaInstance :: class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));
    }

    private function getEntityCondition(DataClass $entity)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance :: class_name(), SchemaInstance :: PROPERTY_ENTITY_TYPE),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($entity :: class_name()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(SchemaInstance :: class_name(), SchemaInstance :: PROPERTY_ENTITY_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($entity->get_id()));

        return $conditions;
    }

    /**
     *
     * @param string $sourceType
     * @param \Chamilo\Core\Metadata\Relation\Storage\DataClass\Relation $relation
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $targetEntity
     * @return integer[]
     */
    public function getSourceRelationIdsForEntity($sourceType, Relation $relation, DataClass $targetEntity)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance :: class_name(), RelationInstance :: PROPERTY_SOURCE_TYPE),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($sourceType));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance :: class_name(), RelationInstance :: PROPERTY_RELATION_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($relation->get_id()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance :: class_name(), RelationInstance :: PROPERTY_TARGET_TYPE),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($targetEntity->class_name()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(RelationInstance :: class_name(), RelationInstance :: PROPERTY_TARGET_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($targetEntity->get_id()));

        $condition = new AndCondition($conditions);

        return DataManager :: distinct(
            RelationInstance :: class_name(),
            new DataClassDistinctParameters($condition, RelationInstance :: PROPERTY_SOURCE_ID));
    }

    public function getVocabularyByElementIdAndUserId(Element $element, User $user)
    {
        $conditions = array();
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_ELEMENT_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($element->get_id()));

        return DataManager :: retrieves(
            Vocabulary :: class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions)));
    }

    public function updateEntitySchemaValues(User $currentUser, RelationService $relationService,
        ElementService $elementService, DataClass $entity, $submittedSchemaValues)
    {
        $availableSchemaIdsForEntity = $this->getAvailableSchemaIdsForEntity($relationService, $entity);

        $submittedSchemaIds = array_keys($submittedSchemaValues);

        $submittedAvailableSchemaIds = array_intersect($submittedSchemaIds, $availableSchemaIdsForEntity);

        foreach ($submittedAvailableSchemaIds as $submittedAvailableSchemaId)
        {
            $schema = DataManager :: retrieve_by_id(Schema :: class_name(), $submittedAvailableSchemaId);
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

    private function processEntitySchema(User $currentUser, Schema $schema, RelationService $relationService,
        ElementService $elementService, DataClass $entity, $submittedSchemaValues)
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
            $schemaInstance = DataManager :: retrieve_by_id(SchemaInstance :: class_name(), $submittedExistingSchemaId);
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

    private function processEntitySchemaInstance(User $currentUser, SchemaInstance $schemaInstance,
        ElementService $elementService, DataClass $entity, $submittedSchemaInstanceValues)
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

    private function processEntityElement(User $currentUser, ElementService $elementService,
        SchemaInstance $schemaInstance, Element $element, DataClass $entity, $submittedElementValues)
    {
        $propertyProviderService = new PropertyProviderService($entity, $schemaInstance);

        try
        {
            $providerLink = $propertyProviderService->getProviderLink($element);
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
                    $entity,
                    $submittedElementValues);
            }
            else
            {
                return $this->processEntityFreeElement(
                    $currentUser,
                    $elementService,
                    $schemaInstance,
                    $element,
                    $entity,
                    $submittedElementValues);
            }
        }
    }

    private function processEntityFreeElement(User $currentUser, ElementService $elementService,
        SchemaInstance $schemaInstance, Element $element, DataClass $entity, $submittedElementValue)
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

    private function processEntityVocabularyElement(User $currentUser, ElementService $elementService,
        SchemaInstance $schemaInstance, Element $element, DataClass $entity, $submittedElementValues)
    {
        $existingElementInstances = $elementService->getElementInstancesForSchemaInstanceAndElement(
            $schemaInstance,
            $element)->as_array();
        $existingElementInstanceIds = array();

        foreach ($existingElementInstances as $existingElementInstance)
        {
            $existingElementInstanceIds[] = $existingElementInstance->get_id();
        }

        $submittedExistingElementInstanceIds = $submittedElementValues[EntityService :: PROPERTY_METADATA_SCHEMA_EXISTING];
        $submittedExistingElementInstanceIds = $submittedExistingElementInstanceIds ? explode(
            ',',
            $submittedExistingElementInstanceIds) : array();

        if ($element->isVocabularyUserDefined())
        {
            $submittedNewElementInstanceValues = $submittedElementValues[EntityService :: PROPERTY_METADATA_SCHEMA_NEW];
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
            $elementInstance = DataManager :: retrieve_by_id(
                ElementInstance :: class_name(),
                $elementInstanceIdToDelete);

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
