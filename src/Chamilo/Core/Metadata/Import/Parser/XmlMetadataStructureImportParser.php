<?php
namespace Chamilo\Core\Metadata\Import\Parser;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\Metadata\Element\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Import\MetadataStructureImportParser;
use Chamilo\Core\Metadata\Schema\Storage\DataClass\Schema;
use Chamilo\Core\Metadata\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;

class XmlMetadataStructureImportParser implements MetadataStructureImportParser
{
    const PARENT_TYPE_ELEMENT = 'element';
    const PARENT_TYPE_ATTRIBUTE = 'attribute';

    /**
     * The XML File
     * 
     * @var string
     */
    private $xml_file;

    /**
     * The XPath Object
     * 
     * @var \DOMXPath
     */
    private $xpath;

    /**
     * The parsed schemas
     * 
     * @var Schema[]
     */
    private $schemas;

    /**
     * The parsed controlled vocabularies
     * 
     * @var ControlledVocabulary[]
     */
    private $controlled_vocabularies;

    /**
     * The parsed elements
     * 
     * @var Element[]
     */
    private $elements;

    /**
     * The parsed attributes
     * 
     * @var Attribute[];
     */
    private $attributes;

    /**
     * Constructs this import parser
     * 
     * @param string $xml_file
     */
    public function __construct($xml_file)
    {
        $this->xml_file = $xml_file;
    }

    /**
     * Parses the imported data
     */
    public function parse()
    {
        $dom_document = new \DOMDocument('1.0', 'UTF-8');
        $dom_document->preserveWhiteSpace = false;
        $dom_document->load($this->xml_file);
        
        $this->xpath = new \DOMXPath($dom_document);
        
        $this->parse_controlled_vocabularies();
        $this->parse_schemas();
        $this->parse_relations_for_elements_and_attributes();
    }

    /**
     * Returns an array of the imported schemas
     * 
     * @return Schema[]
     */
    public function get_schemas()
    {
        return $this->schemas;
    }

    /**
     * Returns an array of the imported controlled_vocabularies
     * 
     * @return ControlledVocabulary[]
     */
    public function get_controlled_vocabularies()
    {
        return $this->controlled_vocabularies;
    }

    /**
     * Returns the elements
     * 
     * @return Element[]
     */
    public function get_elements()
    {
        return $this->elements;
    }

    /**
     * Returns the attributes
     * 
     * @return Attribute[]
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * Parses the schemas from the DOMDocument
     */
    protected function parse_controlled_vocabularies()
    {
        $controlled_vocabulary_nodes = $this->xpath->query(
            '/metadata_structure/controlled_vocabularies/controlled_vocabulary');
        
        foreach ($controlled_vocabulary_nodes as $controlled_vocabulary_node)
        {
            $controlled_vocabulary = new ControlledVocabulary();
            $controlled_vocabulary->set_value($controlled_vocabulary_node->nodeValue);
            
            $this->controlled_vocabularies[$controlled_vocabulary_node->attributes->getNamedItem('id')->nodeValue] = $controlled_vocabulary;
        }
    }

    /**
     * Parses the schemas from the DOMDocument
     */
    protected function parse_schemas()
    {
        $schema_nodes = $this->xpath->query('/metadata_structure/schemas/schema');
        foreach ($schema_nodes as $schema_node)
        {
            $schema = new Schema();
            
            foreach ($schema_node->attributes as $attribute_name => $attribute_value)
            {
                $schema->set_default_property($attribute_name, $attribute_value->nodeValue);
            }
            
            $this->parse_elements_for_schema($schema, $schema_node);
            $this->parse_attributes_for_schema($schema, $schema_node);
            
            $this->schemas[] = $schema;
        }
    }

    /**
     * Parses the elements for the given schema
     * 
     * @param Schema $schema
     * @param \DOMNode $schema_node
     */
    protected function parse_elements_for_schema(Schema $schema,\DOMNode $schema_node)
    {
        $elements = array();
        
        $element_nodes = $this->xpath->query('elements/element', $schema_node);
        foreach ($element_nodes as $element_node)
        {
            $element = new Element();
            $element->set_name($element_node->attributes->getNamedItem('name')->nodeValue);
            
            $this->elements[$element_node->attributes->getNamedItem('id')->nodeValue] = $element;
            $elements[] = $element;
        }
        
        $schema->set_elements($elements);
    }

    /**
     * Parses the attributes for the given schema
     * 
     * @param Schema $schema
     * @param \DOMNode $schema_node
     */
    protected function parse_attributes_for_schema(Schema $schema,\DOMNode $schema_node)
    {
        $attributes = array();
        
        $attribute_nodes = $this->xpath->query('attributes/attribute', $schema_node);
        foreach ($attribute_nodes as $attribute_node)
        {
            $attribute = new Attribute();
            $attribute->set_name($attribute_node->attributes->getNamedItem('name')->nodeValue);
            
            $this->attributes[$attribute_node->attributes->getNamedItem('id')->nodeValue] = $attribute;
            $attributes[] = $attribute;
        }
        
        $schema->set_attributes($attributes);
    }

    /**
     * Parses the relations for the elements (nested_elements, attributes, default_values, controlled_vocabularies)
     * and the attributes (default_values, controlled_vocabularies)
     */
    protected function parse_relations_for_elements_and_attributes()
    {
        $this->parse_nested_elements();
        $this->parse_attribute_relations();
        $this->parse_controlled_vocabulary_relations();
        $this->parse_default_values_relations();
    }

    /**
     * Parses the nested elements and adds them to the correct parent object
     */
    protected function parse_nested_elements()
    {
        $elements = $this->get_elements();
        
        $this->parse_relations(
            '//nested_elements/nested_element', 
            function ($relation_node) use($elements)
            {
                return $elements[$relation_node->Attributes->GetNamedItem('idref')->NodeValue];
            }, 
            function ($parent_element, $relation_objects_for_element)
            {
                $parent_element->set_nested_elements($relation_objects_for_element);
            });
    }

    /**
     * Parses the attributes relations and adds them to the correct parent object
     */
    protected function parse_attribute_relations()
    {
        $attributes = $this->get_attributes();
        
        $this->parse_relations(
            '//element/attributes/attribute', 
            function ($relation_node) use($attributes)
            {
                return $attributes[$relation_node->Attributes->GetNamedItem('idref')->NodeValue];
            }, 
            function ($parent_element, $relation_objects_for_element)
            {
                $parent_element->set_attributes($relation_objects_for_element);
            });
    }

    /**
     * Parses the controlled vocabulary relations and adds them to the correct parent object
     */
    protected function parse_controlled_vocabulary_relations()
    {
        $controlled_vocabularies = $this->controlled_vocabularies;
        
        $this->parse_relations(
            '//element/controlled_vocabularies/controlled_vocabulary |
            //attribute/controlled_vocabularies/controlled_vocabulary', 
            function ($relation_node) use($controlled_vocabularies)
            {
                return $controlled_vocabularies[$relation_node->Attributes->GetNamedItem('idref')->NodeValue];
            }, 
            function ($parent_element, $relation_objects_for_element)
            {
                $parent_element->set_controlled_vocabulary($relation_objects_for_element);
            });
    }

    /**
     * Parses the default values relations and adds them to the correct parent object
     */
    protected function parse_default_values_relations()
    {
        $controlled_vocabularies = $this->get_controlled_vocabularies();
        
        $this->parse_relations(
            '//default_values/default_value', 
            function ($relation_node, $parent_node_type) use($controlled_vocabularies)
            {
                $default_value = ($parent_node_type == XmlMetadataStructureImportParser :: PARENT_TYPE_ELEMENT ? new DefaultElementValue() : new DefaultAttributeValue());
                
                $idref = $relation_node->attributes->getNamedItem('idref');
                if ($idref)
                {
                    $default_value->set_controlled_vocabulary($controlled_vocabularies[$idref->nodeValue]);
                }
                else
                {
                    $default_value->set_value($relation_node->nodeValue);
                }
                
                return $default_value;
            }, 
            function ($parent_element, $relation_objects_for_element)
            {
                $parent_element->set_default_values($relation_objects_for_element);
            });
    }

    /**
     * Helper function to process the relations of an element or an attribute
     * 
     * @param string $xpath_query - The XPath query to scan for the relations
     * @param \Closure $get_relation_object_callback - The callback to return the relation object for the current node
     * @param \Closure $set_relations_callback - The callback to set the relations in the parent element
     */
    protected function parse_relations($xpath_query, $get_relation_object_callback, $set_relations_callback)
    {
        $relation_objects = array();
        
        $relation_nodes = $this->xpath->query($xpath_query);
        foreach ($relation_nodes as $relation_node)
        {
            $parent_node = $relation_node->parentNode->parentNode;
            $parent_object_id = $parent_node->attributes->getNamedItem('id')->nodeValue;
            
            $parent_node_type = $parent_node->nodeName;
            
            $relation_objects[$parent_node_type][$parent_object_id][] = $get_relation_object_callback(
                $relation_node, 
                $parent_node_type);
        }
        
        foreach ($relation_objects as $node_type => $relation_objects_for_node_type)
        {
            $container = $node_type == self :: PARENT_TYPE_ELEMENT ? $this->elements : $this->attributes;
            
            foreach ($relation_objects_for_node_type as $parent_object_id => $relation_objects_for_parent_object)
            {
                $parent_object = $container[$parent_object_id];
                $set_relations_callback($parent_object, $relation_objects_for_parent_object);
            }
        }
    }
}