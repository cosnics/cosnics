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
     * @param SchemaInstance $schema
     * @return Element[]
     */
    public function getElementsForSchemaInstance(SchemaInstance $schemaInstance)
    {
        return $this->getElementsForSchema($schemaInstance->getSchema());
    }

    /**
     *
     * @param Schema $schema
     * @return Element[]
     */
    public function getElementsForSchema(Schema $schema)
    {
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($schema->get_id()));

        return DataManager :: retrieves(
            Element :: class_name(),
            new DataClassRetrievesParameters(
                $condition,
                null,
                null,
                array(
                    new OrderBy(
                        new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_DISPLAY_ORDER)))));
    }

    /**
     *
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @return \libraries\storage\ResultSet
     */
    public function getElementInstancesForSchemaInstanceAndElement(SchemaInstance $schemaInstance, Element $element)
    {
        return DataManager :: retrieves(
            ElementInstance :: class_name(),
            new DataClassRetrievesParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element)));
    }

    /**
     *
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @return \libraries\storage\DataClass
     */
    public function getElementInstanceForSchemaInstanceAndElement(SchemaInstance $schemaInstance, Element $element)
    {
        return DataManager :: retrieve(
            ElementInstance :: class_name(),
            new DataClassRetrieveParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element)));
    }

    /**
     *
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @return \libraries\storage\ResultSet
     */
    public function getElementInstanceVocabulariesForSchemaInstanceAndElement(SchemaInstance $schemaInstance,
        Element $element)
    {
        $join = new Joins();
        $join->add(
            new Join(
                ElementInstance :: class_name(),
                new ComparisonCondition(
                    new PropertyConditionVariable(
                        ElementInstance :: class_name(),
                        ElementInstance :: PROPERTY_VOCABULARY_ID),
                    ComparisonCondition :: EQUAL,
                    new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_ID))));

        return DataManager :: retrieves(
            Vocabulary :: class_name(),
            new DataClassRetrievesParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element),
                null,
                null,
                array(),
                $join));
    }

    /**
     *
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @return \libraries\storage\DataClass
     */
    public function getElementInstanceVocabularyForSchemaInstanceAndElement(SchemaInstance $schemaInstance,
        Element $element)
    {
        $join = new Joins();
        $join->add(
            new Join(
                ElementInstance :: class_name(),
                new ComparisonCondition(
                    new PropertyConditionVariable(
                        ElementInstance :: class_name(),
                        ElementInstance :: PROPERTY_VOCABULARY_ID),
                    ComparisonCondition :: EQUAL,
                    new PropertyConditionVariable(Vocabulary :: class_name(), Vocabulary :: PROPERTY_ID))));

        return DataManager :: retrieve(
            Vocabulary :: class_name(),
            new DataClassRetrieveParameters(
                $this->getElementInstanceConditionForSchemaInstanceAndElement($schemaInstance, $element),
                array(),
                $join));
    }

    /**
     *
     * @param SchemaInstance $schemaInstance
     * @param Element $element
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    private function getElementInstanceConditionForSchemaInstanceAndElement(SchemaInstance $schemaInstance,
        Element $element)
    {
        $conditions = array();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(
                ElementInstance :: class_name(),
                ElementInstance :: PROPERTY_SCHEMA_INSTANCE_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($schemaInstance->get_id()));
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(ElementInstance :: class_name(), ElementInstance :: PROPERTY_ELEMENT_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($element->get_id()));

        return new AndCondition($conditions);
    }

    /**
     *
     * @param int $schemaId
     * @param string $elementName
     */
    public function getElementBySchemaIdAndName($schemaId, $elementName)
    {
        $conditions = array();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_SCHEMA_ID),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($schemaId));

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Element :: class_name(), Element :: PROPERTY_NAME),
            ComparisonCondition :: EQUAL,
            new StaticConditionVariable($elementName));

        $condition = new AndCondition($conditions);

        return DataManager :: retrieve(Element :: class_name(), new DataClassRetrieveParameters($condition));
    }
}
