<?php
namespace Chamilo\Core\MetadataOld\Value\Form\Handler;

use Chamilo\Core\MetadataOld\Value\Form\Helper\ValueEditorFormExportValuesCleaner;
use Chamilo\Core\MetadataOld\Value\Form\ValueEditorFormBuilder;
use Chamilo\Core\MetadataOld\Value\Storage\DataClass\ElementValue;
use Chamilo\Core\MetadataOld\Value\ValueCreator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Handles the metadata value editor form's export values
 * 
 * @package core\metadata\value
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ValueEditorFormHandler
{

    /**
     * The object that is responsible to create the values in the database
     * 
     * @var ValueCreator
     */
    private $value_creator;

    /**
     * Constructor
     * 
     * @param ValueCreator $value_creator
     */
    function __construct(ValueCreator $value_creator)
    {
        $this->value_creator = $value_creator;
    }

    /**
     * Handles the export values from the metadata form
     * 
     * @param array $export_values
     */
    public function handle_form(array $export_values = array())
    {
        $value_cleaner = new ValueEditorFormExportValuesCleaner();
        $export_values = $value_cleaner->clean_export_values($export_values);
        
        $this->handle_elements($export_values[ValueEditorFormBuilder :: FORM_ELEMENT_METADATA_PREFIX]);
    }

    /**
     * Parses the elements from the export values
     * 
     * @param array $export_values
     *
     * @throws \Exception
     */
    protected function handle_elements(array $export_values = array())
    {
        foreach ($export_values as $fully_qualified_element_name => $export_values_for_element)
        {
            $element_value = $this->get_value_creator()->create_element_value_object();
            
            \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: create_element_value_by_fully_qualified_element_name_and_value(
                $element_value, 
                $fully_qualified_element_name, 
                $export_values_for_element[ValueEditorFormBuilder :: FORM_ELEMENT_VALUE]);
            
            $this->handle_attributes_for_element($element_value, $export_values_for_element);
        }
    }

    /**
     * Handles the attributes for the given element export values
     * 
     * @param ElementValue $element_value
     * @param array $export_values_for_element
     *
     * @throws \Exception
     */
    protected function handle_attributes_for_element(ElementValue $element_value, 
        array $export_values_for_element = array())
    {
        foreach ($export_values_for_element as $fully_qualified_attribute_name => $value)
        {
            // Check for invalid attributes
            if (empty($value) || $fully_qualified_attribute_name == ValueEditorFormBuilder :: FORM_ELEMENT_VALUE)
            {
                continue;
            }
            
            $attribute = \Chamilo\Core\MetadataOld\Storage\DataManager :: retrieve_attribute_by_fully_qualified_attribute_name(
                $fully_qualified_attribute_name);
            
            $has_controlled_vocabulary = \Chamilo\Core\MetadataOld\Attribute\Storage\DataManager :: attribute_has_controlled_vocabulary(
                $attribute->get_id());
            
            $attribute_value = $this->get_value_creator()->create_attribute_value_object();
            $attribute_value->set_attribute_id($attribute->get_id());
            $attribute_value->set_element_value_id($element_value->get_id());
            
            if ($has_controlled_vocabulary)
            {
                $attribute_value->set_attribute_vocabulary_id($value);
            }
            else
            {
                $attribute_value->set_value($value);
            }
            
            if (! $attribute_value->create())
            {
                throw new \Exception(
                    Translation :: get(
                        'ObjectNotCreated', 
                        array('OBJECT' => Translation :: get('ElementValue')), 
                        Utilities :: COMMON_LIBRARIES));
            }
        }
    }

    /**
     * Sets the value creator
     * 
     * @param \Chamilo\Core\MetadataOld\ValueCreator $value_creator
     *
     * @throws \InvalidArgumentException
     */
    public function set_value_creator($value_creator)
    {
        if (! $value_creator instanceof ValueCreator)
        {
            throw new \InvalidArgumentException('The given value creator is not an instance of ValueCreator');
        }
        $this->value_creator = $value_creator;
    }

    /**
     * Returns the value creator
     * 
     * @return \Chamilo\Core\MetadataOld\ValueCreator
     */
    public function get_value_creator()
    {
        return $this->value_creator;
    }
}