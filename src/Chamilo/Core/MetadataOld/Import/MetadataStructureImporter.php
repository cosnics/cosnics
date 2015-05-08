<?php
namespace Chamilo\Core\MetadataOld\Import;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\AttributeControlledVocabulary;
use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementNesting;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\ElementRelAttribute;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Core\MetadataOld\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Imports the metadata structure into the system, uses checks for already existing structure
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent.
 */
class MetadataStructureImporter
{

    /**
     *
     * @var MetadataStructureImportParser
     */
    private $metadata_structure_import_parser;

    /**
     * Constructor
     * 
     * @param MetadataStructureImportParser $metadata_structure_import_parser
     */
    public function __construct(MetadataStructureImportParser $metadata_structure_import_parser)
    {
        $this->set_metadata_structure_import_parser($metadata_structure_import_parser);
    }

    /**
     * Sets the metadata structure import parser.
     * Class must be an instance of MetadataStructureImportParser
     * 
     * @param \Chamilo\Core\MetadataOld\MetadataStructureImportParser $metadata_structure_import_parser
     *
     * @throws \InvalidArgumentException
     */
    public function set_metadata_structure_import_parser($metadata_structure_import_parser)
    {
        if (! $metadata_structure_import_parser instanceof MetadataStructureImportParser)
        {
            throw new \InvalidArgumentException(
                'The given metadata_structure_import_parser must be an instance of MetadataStructureImportParser');
        }
        $this->metadata_structure_import_parser = $metadata_structure_import_parser;
    }

    /**
     * Returns the metadata structure import parser
     * 
     * @return \Chamilo\Core\MetadataOld\MetadataStructureImportParser
     */
    public function get_metadata_structure_import_parser()
    {
        return $this->metadata_structure_import_parser;
    }

    /**
     * Imports the data using the metadata structure import parser and checks for doubles
     */
    public function import()
    {
        $this->metadata_structure_import_parser->parse();
        
        $this->create_controlled_vocabularies();
        $this->create_schemas();
        $this->create_relations();
    }

    /**
     * Creates the controlled vocabularies
     * 
     * @throws \Exception
     */
    protected function create_controlled_vocabularies()
    {
        $controlled_vocabularies = $this->metadata_structure_import_parser->get_controlled_vocabularies();
        foreach ($controlled_vocabularies as $controlled_vocabulary)
        {
            $existing_controlled_vocabulary = \Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataManager :: retrieve_controlled_vocabulary_by_value(
                $controlled_vocabulary->get_value());
            
            if (! $existing_controlled_vocabulary instanceof ControlledVocabulary)
            {
                if (! $controlled_vocabulary->create())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotCreated', 
                            array('OBJECT' => Translation :: get('ControlledVocabulary')), 
                            Utilities :: COMMON_LIBRARIES));
                }
            }
            else
            {
                $controlled_vocabulary->set_id($existing_controlled_vocabulary->get_id());
            }
        }
    }

    /**
     * Creates the schemas
     * 
     * @throws \Exception
     */
    protected function create_schemas()
    {
        $schemas = $this->metadata_structure_import_parser->get_schemas();
        foreach ($schemas as $schema)
        {
            $existing_schema = \Chamilo\Core\MetadataOld\Schema\Storage\DataManager :: retrieve_schema_by_namespace(
                $schema->get_namespace());
            
            if (! $existing_schema instanceof Schema)
            {
                if (! $schema->create())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotCreated', 
                            array('OBJECT' => Translation :: get('Schema')), 
                            Utilities :: COMMON_LIBRARIES));
                }
            }
            else
            {
                $schema->set_id($existing_schema->get_id());
            }
            
            $this->create_elements_for_schema($schema);
            $this->create_attributes_for_schema($schema);
        }
    }

    /**
     * Creates the elements for the given schema
     * 
     * @param Schema $schema
     *
     * @throws \Exception
     */
    protected function create_elements_for_schema(Schema $schema)
    {
        $elements = $schema->get_elements();
        foreach ($elements as $element)
        {
            $existing_element = \Chamilo\Core\MetadataOld\Storage\DataManager :: retrieve_element_by_schema_id_and_name(
                $schema->get_id(), 
                $element->get_name());
            
            if (! $existing_element instanceof Element)
            {
                $element->set_schema_id($schema->get_id());
                
                if (! $element->create())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotCreated', 
                            array('OBJECT' => Translation :: get('Element')), 
                            Utilities :: COMMON_LIBRARIES));
                }
            }
            else
            {
                $element->set_id($existing_element->get_id());
            }
        }
    }

    /**
     * Creates the attributes for the given schema
     * 
     * @param Schema $schema
     *
     * @throws \Exception
     */
    protected function create_attributes_for_schema(Schema $schema)
    {
        $attributes = $schema->get_attributes();
        foreach ($attributes as $attribute)
        {
            $existing_attribute = \Chamilo\Core\MetadataOld\Attribute\Storage\DataManager :: retrieve_attribute_by_schema_id_and_name(
                $schema->get_id(), 
                $attribute->get_name());
            
            if (! $existing_attribute instanceof Attribute)
            {
                $attribute->set_schema_id($schema->get_id());
                
                if (! $attribute->create())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotCreated', 
                            array('OBJECT' => Translation :: get('Attribute')), 
                            Utilities :: COMMON_LIBRARIES));
                }
            }
            else
            {
                $attribute->set_id($existing_attribute->get_id());
            }
        }
    }

    /**
     * Creates the relations for the elements and the attributes
     */
    protected function create_relations()
    {
        $schemas = $this->get_metadata_structure_import_parser()->get_schemas();
        
        foreach ($schemas as $schema)
        {
            $attributes = $schema->get_attributes();
            $elements = $schema->get_elements();
            
            $this->create_controlled_vocabulary_for_elements($elements);
            $this->create_controlled_vocabulary_for_attributes($attributes);
            
            $this->create_default_values_for_elements($elements);
            $this->create_default_values_for_attributes($attributes);
            
            $this->create_nested_elements_for_elements($elements);
            $this->create_attributes_for_elements($elements);
        }
    }

    /**
     * Creates the controlled vocabulary for elements
     * 
     * @param Element[] $elements
     *
     * @throws \Exception
     */
    protected function create_controlled_vocabulary_for_elements($elements)
    {
        foreach ($elements as $element)
        {
            $controlled_vocabulary = $element->get_controlled_vocabulary();
            foreach ($controlled_vocabulary as $controlled_vocabulary_object)
            {
                $element_controlled_vocabulary = \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: retrieve_element_controlled_vocabulary_by_element_and_controlled_vocabulary(
                    $element->get_id(), 
                    $controlled_vocabulary_object->get_id());
                
                if (! $element_controlled_vocabulary instanceof ElementControlledVocabulary)
                {
                    $element_controlled_vocabulary = new ElementControlledVocabulary();
                    
                    $element_controlled_vocabulary->set_element_id($element->get_id());
                    $element_controlled_vocabulary->set_controlled_vocabulary_id(
                        $controlled_vocabulary_object->get_id());
                    
                    if (! $element_controlled_vocabulary->create())
                    {
                        throw new \Exception(
                            Translation :: get(
                                'ObjectNotCreated', 
                                array('OBJECT' => Translation :: get('ElementControlledVocabulary')), 
                                Utilities :: COMMON_LIBRARIES));
                    }
                }
            }
        }
    }

    /**
     * Creates the controlled vocabulary for attributes
     * 
     * @param Attribute[] $attributes
     *
     * @throws \Exception
     */
    protected function create_controlled_vocabulary_for_attributes($attributes)
    {
        foreach ($attributes as $attribute)
        {
            $controlled_vocabulary = $attribute->get_controlled_vocabulary();
            foreach ($controlled_vocabulary as $controlled_vocabulary_object)
            {
                $attribute_controlled_vocabulary = \Chamilo\Core\MetadataOld\Attribute\Storage\DataManager :: retrieve_attribute_controlled_vocabulary_by_attribute_and_controlled_vocabulary(
                    $attribute->get_id(), 
                    $controlled_vocabulary_object->get_id());
                
                if (! $attribute_controlled_vocabulary instanceof AttributeControlledVocabulary)
                {
                    $attribute_controlled_vocabulary = new AttributeControlledVocabulary();
                    
                    $attribute_controlled_vocabulary->set_attribute_id($attribute->get_id());
                    $attribute_controlled_vocabulary->set_controlled_vocabulary_id(
                        $controlled_vocabulary_object->get_id());
                    
                    if (! $attribute_controlled_vocabulary->create())
                    {
                        throw new \Exception(
                            Translation :: get(
                                'ObjectNotCreated', 
                                array('OBJECT' => Translation :: get('AttributeControlledVocabulary')), 
                                Utilities :: COMMON_LIBRARIES));
                    }
                }
            }
        }
    }

    /**
     * Creates the nested elements for elements
     * 
     * @param Element[] $elements
     *
     * @throws \Exception
     */
    protected function create_nested_elements_for_elements($elements)
    {
        foreach ($elements as $element)
        {
            $nested_elements = $element->get_nested_elements();
            foreach ($nested_elements as $nested_element)
            {
                $element_nesting = \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: retrieve_element_nesting_by_parent_and_child_element(
                    $element->get_id(), 
                    $nested_element->get_id());
                
                if (! $element_nesting instanceof ElementNesting)
                {
                    $element_nesting = new ElementNesting();
                    $element_nesting->set_parent_element_id($element->get_id());
                    $element_nesting->set_child_element_id($nested_element->get_id());
                    
                    if (! $element_nesting->create())
                    {
                        throw new \Exception(
                            Translation :: get(
                                'ObjectNotCreated', 
                                array('OBJECT' => Translation :: get('ElementNesting')), 
                                Utilities :: COMMON_LIBRARIES));
                    }
                }
            }
        }
    }

    /**
     * Creates the attributes for elements
     * 
     * @param Element[] $elements
     *
     * @throws \Exception
     */
    protected function create_attributes_for_elements($elements)
    {
        foreach ($elements as $element)
        {
            $attributes = $element->get_attributes();
            foreach ($attributes as $attribute)
            {
                $element_rel_attribute = \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: retrieve_element_rel_attribute_by_element_and_attribute(
                    $element->get_id(), 
                    $attribute->get_id());
                
                if (! $element_rel_attribute instanceof ElementRelAttribute)
                {
                    $element_rel_attribute = new ElementRelAttribute();
                    
                    $element_rel_attribute->set_element_id($element->get_id());
                    $element_rel_attribute->set_attribute_id($attribute->get_id());
                    
                    if (! $element_rel_attribute->create())
                    {
                        throw new \Exception(
                            Translation :: get(
                                'ObjectNotCreated', 
                                array('OBJECT' => Translation :: get('ElementRelAttribute')), 
                                Utilities :: COMMON_LIBRARIES));
                    }
                }
            }
        }
    }

    /**
     * Creates the default values for elements
     * 
     * @param Element[] $elements
     *
     * @throws \Exception
     */
    protected function create_default_values_for_elements($elements)
    {
        foreach ($elements as $element)
        {
            $default_values = $element->get_default_values();
            foreach ($default_values as $default_value)
            {
                $controlled_vocabulary = $default_value->get_controlled_vocabulary();
                $controlled_vocabulary_id = ($controlled_vocabulary ? $controlled_vocabulary->get_id() : null);
                
                $existing_default_value = \Chamilo\Core\MetadataOld\Value\Element\Storage\DataManager :: retrieve_default_element_value_by_value_or_controlled_vocabulary(
                    $element->get_id(), 
                    $default_value->get_value(), 
                    $controlled_vocabulary_id);
                
                if (! $existing_default_value instanceof DefaultElementValue)
                {
                    $default_value->set_element_id($element->get_id());
                    if ($controlled_vocabulary)
                    {
                        $default_value->set_element_vocabulary_id($controlled_vocabulary_id);
                    }
                    
                    if (! $default_value->create())
                    {
                        throw new \Exception(
                            Translation :: get(
                                'ObjectNotCreated', 
                                array('OBJECT' => Translation :: get('DefaultValue')), 
                                Utilities :: COMMON_LIBRARIES));
                    }
                }
            }
        }
    }

    /**
     * Creates the default values for attributes
     * 
     * @param Attribute[] $attributes
     *
     * @throws \Exception
     */
    protected function create_default_values_for_attributes($attributes)
    {
        foreach ($attributes as $attribute)
        {
            $default_values = $attribute->get_default_values();
            foreach ($default_values as $default_value)
            {
                $controlled_vocabulary = $default_value->get_controlled_vocabulary();
                $controlled_vocabulary_id = ($controlled_vocabulary ? $controlled_vocabulary->get_id() : null);
                
                $existing_default_value = \Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataManager :: retrieve_default_attribute_value_by_value_or_controlled_vocabulary(
                    $attribute->get_id(), 
                    $default_value->get_value(), 
                    $controlled_vocabulary_id);
                
                if (! $existing_default_value instanceof DefaultAttributeValue)
                {
                    $default_value->set_attribute_id($attribute->get_id());
                    if ($controlled_vocabulary)
                    {
                        $default_value->set_attribute_vocabulary_id($controlled_vocabulary_id);
                    }
                    
                    if (! $default_value->create())
                    {
                        throw new \Exception(
                            Translation :: get(
                                'ObjectNotCreated', 
                                array('OBJECT' => Translation :: get('DefaultValue')), 
                                Utilities :: COMMON_LIBRARIES));
                    }
                }
            }
        }
    }
}