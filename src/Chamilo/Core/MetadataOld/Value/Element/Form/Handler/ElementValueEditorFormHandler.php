<?php
namespace Chamilo\Core\MetadataOld\Value\Element\Form\Handler;

use Chamilo\Core\MetadataOld\Value\Element\Form\ElementValueEditorFormBuilder;
use Chamilo\Core\MetadataOld\Value\Form\Helper\ValueEditorFormExportValuesCleaner;
use Chamilo\Core\MetadataOld\Value\ValueCreator;

/**
 * Handles the simple element form's export values
 * 
 * @package core\metadata\element
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementValueEditorFormHandler
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
    public function handle_form($export_values = array())
    {
        $value_cleaner = new ValueEditorFormExportValuesCleaner();
        $export_values = $value_cleaner->clean_export_values($export_values);
        
        $this->handle_elements($export_values[ElementValueEditorFormBuilder :: FORM_ELEMENT_METADATA_PREFIX]);
    }

    /**
     * Parses the elements from the export values
     * 
     * @param array $export_values
     *
     * @throws \Exception
     */
    protected function handle_elements($export_values = array())
    {
        foreach ($export_values as $fully_qualified_element_name => $export_value)
        {
            $element_value = $this->get_value_creator()->create_element_value_object();
            \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: create_element_value_by_fully_qualified_element_name_and_value(
                $element_value, 
                $fully_qualified_element_name, 
                $export_value);
        }
    }

    /**
     * Sets the value creator
     * 
     * @param ValueCreator $value_creator
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
     * @return ValueCreator
     */
    public function get_value_creator()
    {
        return $this->value_creator;
    }
}