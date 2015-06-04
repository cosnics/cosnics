<?php
namespace Chamilo\Core\MetadataOld\Export\Renderer;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Export\MetadataStructureExportRenderer;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * Implementation of the metadata structure export renderer for xml
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class XmlMetadataStructureExportRenderer implements MetadataStructureExportRenderer
{

    /**
     *
     * @var \DOMDocument
     */
    private $dom_document;

    /**
     * Renders the metadata structure export
     * 
     * @param Schema[]   $schemas
     * @param ControlledVocabulary[] $controlled_vocabularies
     *
     * @return string
     */
    public function render(array $schemas, array $controlled_vocabularies)
    {
        $this->dom_document = new \DOMDocument('1.0', 'utf-8');
        
        $root_node = $this->render_root_node();
        $this->render_schemas($schemas, $root_node);
        $this->render_controlled_vocabularies($controlled_vocabularies, $root_node);
        
        $file_path = Path :: getInstance()->getTemporaryPath() . Session :: get_user_id() . '/metadata.xml';
        $this->dom_document->save($file_path);
        
        return $file_path;
    }

    /**
     * Renders the root node
     * 
     * @return \DOMElement
     */
    protected function render_root_node()
    {
        return $this->create_element_and_add_to_node($this->dom_document, 'metadata_structure');
    }

    /**
     * Renders the schemas
     * 
     * @param Schema[] $schemas
     * @param \DOMElement $root_node
     */
    protected function render_schemas(array $schemas, \DOMElement $root_node)
    {
        $schemas_node = $this->create_element_and_add_to_node($root_node, 'schemas');
        
        foreach ($schemas as $schema)
        {
            $schema_attributes = array(
                'name' => $schema->get_name(), 
                'namespace' => $schema->get_namespace(), 
                'url' => $schema->get_url());
            
            $schema_node = $this->create_element_and_add_to_node($schemas_node, 'schema', null, $schema_attributes);
            
            $this->render_elements_for_schema($schema, $schema_node);
            $this->render_attributes_for_schema($schema, $schema_node);
        }
    }

    /**
     * Renders the elements for a given schema
     * 
     * @param Schema $schema
     * @param \DOMElement $schema_node
     */
    protected function render_elements_for_schema(Schema $schema, \DOMElement $schema_node)
    {
        $elements_node = $this->create_element_and_add_to_node($schema_node, 'elements');
        
        foreach ($schema->get_elements() as $element)
        {
            $element_attributes = array(
                'id' => $element->get_id(), 
                'name' => $element->get_name(), 
                'display_order' => $element->get_display_order());
            
            $element_node = $this->create_element_and_add_to_node($elements_node, 'element', null, $element_attributes);
            
            $this->render_nested_elements_for_element($element, $element_node);
            $this->render_attributes_for_element($element, $element_node);
            $this->render_default_values_for_element($element, $element_node);
            $this->render_controlled_vocabulary_for_element($element, $element_node);
        }
    }

    /**
     * Renders the nested elements for a given element
     * 
     * @param Element $element
     * @param \DOMElement $element_node
     */
    protected function render_nested_elements_for_element(Element $element, \DOMElement $element_node)
    {
        $elements_node = $this->create_element_and_add_to_node($element_node, 'nested_elements');
        
        foreach ($element->get_nested_elements() as $element)
        {
            $element_attributes = array('idref' => $element->get_id());
            
            $this->create_element_and_add_to_node($elements_node, 'nested_element', null, $element_attributes);
        }
    }

    /**
     * Renders the attributes for a given element
     * 
     * @param Element $element
     * @param \DOMElement $element_node
     */
    protected function render_attributes_for_element(Element $element, \DOMElement $element_node)
    {
        $attributes_node = $this->create_element_and_add_to_node($element_node, 'attributes');
        
        foreach ($element->get_attributes() as $attribute)
        {
            $attribute_node_attributes = array('idref' => $attribute->get_id());
            
            $this->create_element_and_add_to_node($attributes_node, 'attribute', null, $attribute_node_attributes);
        }
    }

    /**
     * Renders the default values for a given element
     * 
     * @param Element $element
     * @param \DOMElement $element_node
     */
    protected function render_default_values_for_element(Element $element, \DOMElement $element_node)
    {
        $default_values_node = $this->create_element_and_add_to_node($element_node, 'default_values');
        
        foreach ($element->get_default_values() as $default_value)
        {
            if ($default_value->get_element_vocabulary_id())
            {
                $default_value_attributes = array('idref' => $default_value->get_element_vocabulary_id());
                $value = null;
            }
            else
            {
                $value = $default_value->get_value();
                $default_value_attributes = array();
            }
            
            $this->create_element_and_add_to_node(
                $default_values_node, 
                'default_value', 
                $value, 
                $default_value_attributes);
        }
    }

    /**
     * Renders the controlled vocabulary for a given element
     * 
     * @param Element $element
     * @param \DOMElement $element_node
     */
    protected function render_controlled_vocabulary_for_element(Element $element, \DOMElement $element_node)
    {
        $controlled_vocabularies_node = $this->create_element_and_add_to_node($element_node, 'controlled_vocabularies');
        
        foreach ($element->get_controlled_vocabulary() as $controlled_vocabulary)
        {
            $controlled_vocabularies_node_attributes = array('idref' => $controlled_vocabulary->get_id());
            
            $this->create_element_and_add_to_node(
                $controlled_vocabularies_node, 
                'controlled_vocabulary', 
                null, 
                $controlled_vocabularies_node_attributes);
        }
    }

    /**
     * Renders the attributes for a given schema
     * 
     * @param Schema $schema
     * @param \DOMElement $schema_node
     */
    protected function render_attributes_for_schema(Schema $schema, \DOMElement $schema_node)
    {
        $attributes_node = $this->create_element_and_add_to_node($schema_node, 'attributes');
        
        foreach ($schema->get_attributes() as $attribute)
        {
            $attribute_node_attributes = array('id' => $attribute->get_id(), 'name' => $attribute->get_name());
            $attribute_node = $this->create_element_and_add_to_node(
                $attributes_node, 
                'attribute', 
                null, 
                $attribute_node_attributes);
            
            $this->render_default_values_for_attribute($attribute, $attribute_node);
            $this->render_controlled_vocabulary_for_attribute($attribute, $attribute_node);
        }
    }

    /**
     * Renders the default values for a given attribute
     * 
     * @param Attribute $attribute
     * @param \DOMElement $attribute_node
     */
    protected function render_default_values_for_attribute(Attribute $attribute, \DOMElement $attribute_node)
    {
        $default_values_node = $this->create_element_and_add_to_node($attribute_node, 'default_values');
        
        foreach ($attribute->get_default_values() as $default_value)
        {
            if ($default_value->get_attribute_vocabulary_id())
            {
                $default_value_attributes = array('idref' => $default_value->get_attribute_vocabulary_id());
                $value = null;
            }
            else
            {
                $value = $default_value->get_value();
                $default_value_attributes = array();
            }
            
            $this->create_element_and_add_to_node(
                $default_values_node, 
                'default_value', 
                $value, 
                $default_value_attributes);
        }
    }

    /**
     * Renders the controlled vocabulary for a given attribute
     * 
     * @param Attribute $attribute
     * @param \DOMElement $attribute_node
     */
    protected function render_controlled_vocabulary_for_attribute(Attribute $attribute, \DOMElement $attribute_node)
    {
        $controlled_vocabularies_node = $this->create_element_and_add_to_node(
            $attribute_node, 
            'controlled_vocabularies');
        
        foreach ($attribute->get_controlled_vocabulary() as $controlled_vocabulary)
        {
            $controlled_vocabularies_node_attributes = array('idref' => $controlled_vocabulary->get_id());
            
            $this->create_element_and_add_to_node(
                $controlled_vocabularies_node, 
                'controlled_vocabulary', 
                null, 
                $controlled_vocabularies_node_attributes);
        }
    }

    /**
     * Renders the controlled vocabularies
     * 
     * @param ControlledVocabulary[] $controlled_vocabularies
     * @param \DOMElement $root_node
     */
    protected function render_controlled_vocabularies(array $controlled_vocabularies, \DOMElement $root_node)
    {
        $controlled_vocabularies_node = $this->create_element_and_add_to_node($root_node, 'controlled_vocabularies');
        
        foreach ($controlled_vocabularies as $controlled_vocabulary)
        {
            $this->create_element_and_add_to_node(
                $controlled_vocabularies_node, 
                'controlled_vocabulary', 
                $controlled_vocabulary->get_value(), 
                array('id' => $controlled_vocabulary->get_id()));
        }
    }

    /**
     * ***************************************************************************************************************
     * Helper functionality for creation of xml nodes *
     * **************************************************************************************************************
     */
    
    /**
     * Helper function which creates an element and defines its attributes by a given element name and attributes array
     * which is defined as attribute_name => attribute_value.
     * This function adds the created element to a given parent node
     * 
     * @param \DOMElement $parent_node
     * @param string $element_name
     * @param string $element_value
     * @param array $attributes
     *
     * @return \DOMElement
     */
    protected function create_element_and_add_to_node(\DOMElement $parent_node, $element_name, $element_value = null, 
        array $attributes = array())
    {
        $element_node = $this->dom_document->createElement($element_name);
        $element_node->nodeValue = $element_value;
        
        $this->create_attributes_and_add_to_node($attributes, $element_node);
        $parent_node->appendChild($element_node);
        
        return $element_node;
    }

    /**
     * Helper function which creates attributes and adds them to a given node.
     * The attributes are defined as an array
     * with attribute_name => attribute_value
     * 
     * @param array $attributes
     * @param \DOMElement $node
     */
    protected function create_attributes_and_add_to_node(array $attributes, \DOMElement $node)
    {
        foreach ($attributes as $attribute_name => $attribute_value)
        {
            $attribute_node = $this->dom_document->createAttribute($attribute_name);
            $attribute_node->value = $attribute_value;
            
            $node->appendChild($attribute_node);
        }
    }
}