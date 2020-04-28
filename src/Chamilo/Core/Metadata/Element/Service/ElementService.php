<?php
namespace Chamilo\Core\Metadata\Element\Service;

use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\ElementInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
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
class ElementService
{

    /**
     *
     * @param int $schemaId
     * @param string $elementName
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Element
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getElementBySchemaIdAndName($schemaId, $elementName)
    {
        $conditions = array();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_SCHEMA_ID), ComparisonCondition::EQUAL,
            new StaticConditionVariable($schemaId)
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_NAME), ComparisonCondition::EQUAL,
            new StaticConditionVariable($elementName)
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieve(Element::class, new DataClassRetrieveParameters($condition));
    }

    /**
     *
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getElementInstanceConditionForSchemaInstanceAndElement(
        SchemaInstance $schemaInstance, Element $element
    )
    {
        $conditions = array();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(ElementInstance::class, ElementInstance::PROPERTY_SCHEMA_INSTANCE_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($schemaInstance->getId())
        );
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(ElementInstance::class, ElementInstance::PROPERTY_ELEMENT_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($element->getId())
        );

        return new AndCondition($conditions);
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ElementInstance
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getElementInstanceForSchemaInstanceAndElement(SchemaInstance $schemaInstance, Element $element)
    {
        return DataManager::retrieve(
            ElementInstance::class, new DataClassRetrieveParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element)
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary[]
     * @throws \Exception
     */
    public function getElementInstanceVocabulariesForSchemaInstanceAndElement(
        SchemaInstance $schemaInstance, Element $element
    )
    {
        $join = new Joins();
        $join->add(
            new Join(
                ElementInstance::class, new ComparisonCondition(
                    new PropertyConditionVariable(
                        ElementInstance::class, ElementInstance::PROPERTY_VOCABULARY_ID
                    ), ComparisonCondition::EQUAL,
                    new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ID)
                )
            )
        );

        return DataManager::retrieves(
            Vocabulary::class, new DataClassRetrievesParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element), null, null,
                array(), $join
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Vocabulary
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function getElementInstanceVocabularyForSchemaInstanceAndElement(
        SchemaInstance $schemaInstance, Element $element
    )
    {
        $join = new Joins();
        $join->add(
            new Join(
                ElementInstance::class, new ComparisonCondition(
                    new PropertyConditionVariable(
                        ElementInstance::class, ElementInstance::PROPERTY_VOCABULARY_ID
                    ), ComparisonCondition::EQUAL,
                    new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ID)
                )
            )
        );

        return DataManager::retrieve(
            Vocabulary::class, new DataClassRetrieveParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element), array(), $join
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Element $element
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\ElementInstance[]
     * @throws \Exception
     */
    public function getElementInstancesForSchemaInstanceAndElement(SchemaInstance $schemaInstance, Element $element)
    {
        return DataManager::retrieves(
            ElementInstance::class, new DataClassRetrievesParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element)
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\Schema $schema
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Element[]
     * @throws \Exception
     */
    public function getElementsForSchema(Schema $schema)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_SCHEMA_ID), ComparisonCondition::EQUAL,
            new StaticConditionVariable($schema->getId())
        );

        return DataManager::retrieves(
            Element::class, new DataClassRetrievesParameters(
                $condition, null, null, array(
                    new OrderBy(
                        new PropertyConditionVariable(Element::class, Element::PROPERTY_DISPLAY_ORDER)
                    )
                )
            )
        );
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\Element[]
     * @throws \Exception
     */
    public function getElementsForSchemaInstance(SchemaInstance $schemaInstance)
    {
        return $this->getElementsForSchema($schemaInstance->getSchema());
    }
}
