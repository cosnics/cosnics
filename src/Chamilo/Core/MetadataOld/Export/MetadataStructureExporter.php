<?php
namespace Chamilo\Core\MetadataOld\Export;

use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Schema\Storage\DataClass\Schema;
use Chamilo\Core\MetadataOld\Storage\DataManager;

/**
 * Exports the structure of the metadata
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MetadataStructureExporter
{

    /**
     *
     * @var MetadataStructureExportRenderer
     */
    private $metadata_structure_export_renderer;

    /**
     * Constructor
     * 
     * @param MetadataStructureExportRenderer $metadata_structure_export_renderer
     */
    public function __construct(MetadataStructureExportRenderer $metadata_structure_export_renderer)
    {
        $this->set_metadata_structure_export_renderer($metadata_structure_export_renderer);
    }

    /**
     * Sets the metadata structure export renderer, must be an instance of the
     * MetadataStructureExportRenderer interface
     * 
     * @param MetadataStructureExportRenderer $metadata_structure_export_renderer
     *
     * @throws \InvalidArgumentException
     */
    public function set_metadata_structure_export_renderer(
        MetadataStructureExportRenderer $metadata_structure_export_renderer)
    {
        if (! $metadata_structure_export_renderer instanceof MetadataStructureExportRenderer)
        {
            throw new \InvalidArgumentException(
                'The given metadata structure export renderer must be an instance of MetadataStructureExportRenderer');
        }
        
        $this->metadata_structure_export_renderer = $metadata_structure_export_renderer;
    }

    /**
     * Returns the metadata structure export renderer
     * 
     * @return MetadataStructureExportRenderer
     */
    public function get_metadata_structure_export_renderer()
    {
        return $this->metadata_structure_export_renderer;
    }

    /**
     * Exports the metadata structure
     */
    public function export()
    {
        $schemas = DataManager :: retrieves(Schema :: class_name());
        while ($schema = $schemas->next_result())
        {
            $this->add_elements_to_schema_object($schema);
            $this->add_attributes_to_schema_object($schema);
        }
        
        $controlled_vocabularies = DataManager :: retrieves(ControlledVocabulary :: class_name());
        
        return $this->get_metadata_structure_export_renderer()->render(
            $schemas->as_array(), 
            $controlled_vocabularies->as_array());
    }

    /**
     * Adds the elements to the given schema object
     * 
     * @param Schema $schema
     */
    protected function add_elements_to_schema_object(Schema $schema)
    {
        $elements = DataManager :: retrieve_elements_for_schema($schema->get_id());
        while ($element = $elements->next_result())
        {
            $this->add_nested_elements_to_element_object($element);
            $this->add_attributes_to_element_object($element);
            $this->add_default_values_to_element_object($element);
            $this->add_controlled_vocabulary_to_element_object($element);
        }
        
        $schema->set_elements($elements->as_array());
    }

    /**
     * Adds the nested elements to the given element object
     * 
     * @param Element $element
     */
    protected function add_nested_elements_to_element_object(Element $element)
    {
        $nested_elements = DataManager :: retrieve_nested_elements_for_element($element->get_id());
        $element->set_nested_elements($nested_elements->as_array());
    }

    /**
     * Adds the attributes to the given element object
     * 
     * @param Element $element
     */
    protected function add_attributes_to_element_object(Element $element)
    {
        $attributes = DataManager :: retrieve_attributes_for_element($element->get_id());
        $element->set_attributes($attributes->as_array());
    }

    /**
     * Adds the default values to the given element object
     * 
     * @param Element $element
     */
    protected function add_default_values_to_element_object(Element $element)
    {
        $default_values = DataManager :: retrieve_default_values_for_element($element->get_id());
        $element->set_default_values($default_values->as_array());
    }

    /**
     * Adds the controlled vocabulary to the given element object
     * 
     * @param Element $element
     */
    protected function add_controlled_vocabulary_to_element_object(Element $element)
    {
        $controlled_vocabulary = DataManager :: retrieve_controlled_vocabulary_from_element($element->get_id());
        $element->set_controlled_vocabulary($controlled_vocabulary->as_array());
    }

    /**
     * Adds the attributes to the given schema object
     * 
     * @param Schema $schema
     */
    protected function add_attributes_to_schema_object(Schema $schema)
    {
        $attributes = DataManager :: retrieve_attributes_for_schema($schema->get_id());
        while ($attribute = $attributes->next_result())
        {
            $this->add_default_values_to_attribute_object($attribute);
            $this->add_controlled_vocabulary_to_attribute_object($attribute);
        }
        
        $schema->set_attributes($attributes->as_array());
    }

    /**
     * Adds the default values to the given attribute object
     * 
     * @param Attribute $attribute
     */
    protected function add_default_values_to_attribute_object(Attribute $attribute)
    {
        $default_values = DataManager :: retrieve_default_values_for_attribute($attribute->get_id());
        $attribute->set_default_values($default_values->as_array());
    }

    /**
     * Adds the controlled vocabulary to the given attribute object
     * 
     * @param Attribute $attribute
     */
    protected function add_controlled_vocabulary_to_attribute_object(Attribute $attribute)
    {
        $controlled_vocabulary = DataManager :: retrieve_controlled_vocabulary_from_attribute($attribute->get_id());
        $attribute->set_controlled_vocabulary($controlled_vocabulary->as_array());
    }
}