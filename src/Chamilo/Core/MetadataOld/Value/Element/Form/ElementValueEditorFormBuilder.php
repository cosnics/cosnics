<?php
namespace Chamilo\Core\MetadataOld\Value\Element\Form;

use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Value\Element\Storage\DataManager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This form builder builds a simple form based on a given array of elements.
 * This builder only renders the given elements once instead of multiple times.
 * 
 * @package core\metadata\element
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementValueEditorFormBuilder
{
    const FORM_ELEMENT_METADATA_PREFIX = 'md';

    /**
     * The FormValidator reference
     * 
     * @var \libraries\format\FormValidator
     */
    private $form;

    /**
     * Constructor
     * 
     * @param FormValidator $form
     */
    public function __construct(FormValidator $form = null)
    {
        if (! $form)
        {
            $form = new FormValidator('metadata_form');
        }
        
        $this->form = $form;
    }

    /**
     * Builds the form based on the given array of elements and values for the given elements.
     * Optionally possible
     * to use the system's default values of an element.
     * 
     * @param Element[] $elements
     * @param ElementValue[] $element_values
     * @param bool $use_system_default_values
     */
    public function build_form(array $elements, array $element_values = null, $use_system_default_values = true)
    {
        $form = $this->form;
        
        foreach ($elements as $element)
        {
            $element_name = $this->get_element_form_name($element);
            $display_name = $element->get_display_name();
            
            $vocabulary = array();
            
            if (\Chamilo\Core\MetadataOld\Element\Storage\DataManager :: element_has_controlled_vocabulary(
                $element->get_id()))
            {
                $vocabulary[0] = '-- ' . Translation :: get('PickAChoice') . ' --';
                
                $vocabulary += \Chamilo\Core\MetadataOld\Element\Storage\DataManager :: retrieve_controlled_vocabulary_terms_from_element(
                    $element->get_id());
                
                $form->addElement('select', $element_name, $display_name, $vocabulary);
            }
            else
            {
                $form->addElement('text', $element_name, $display_name, array('size' => 50));
            }
            
            if ($element->is_required())
            {
                $form->addRule(
                    $element_name, 
                    Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                    'required');
            }
        }
        
        $this->set_default_values($elements, $element_values, $use_system_default_values);
    }

    /**
     * Sets the default values for the form (Always uses the last element value created per element).
     * Falls back
     * to the default values from the metadata application if no default values have been given for a certain element
     * 
     * @param Element[] $elements
     * @param ElementValue[] $element_values
     * @param bool $use_system_default_values
     */
    protected function set_default_values(array $elements, array $element_values = array(), $use_system_default_values = true)
    {
        $grouped_values = array();
        
        foreach ($element_values as $element_value)
        {
            $grouped_values[$element_value->get_element_id()][] = $element_value->get_element_vocabulary_id() ? $element_value->get_element_vocabulary_id() : $element_value->get_value();
        }
        
        $default_values = array();
        
        foreach ($grouped_values as $element_id => $element_values)
        {
            $element = DataManager :: retrieve_by_id(Element :: class_name(), $element_id);
            $element_form_name = $this->get_element_form_name($element);
            
            arsort($element_values);
            
            $default_values[$element_form_name] = $element_values[0];
        }
        
        if ($use_system_default_values)
        {
            foreach ($elements as $element)
            {
                if (! array_key_exists($element->get_id(), $grouped_values))
                {
                    $default_element_values = DataManager :: retrieve_default_element_value_for_element(
                        $element->get_id());
                    
                    if ($default_element_values->size() > 0)
                    {
                        $default_element_value = $default_element_values->next_result();
                        
                        $element_value = $default_element_value->get_element_vocabulary_id() ? $default_element_value->get_element_vocabulary_id() : $default_element_value->get_value();
                        
                        $element_form_name = $this->get_element_form_name($element);
                        $default_values[$element_form_name] = $element_value;
                    }
                }
            }
        }
        
        $this->form->setDefaults($default_values);
    }

    /**
     * Returns the element form name
     * 
     * @param Element $element
     *
     * @return string
     */
    protected function get_element_form_name(Element $element)
    {
        return self :: FORM_ELEMENT_METADATA_PREFIX . '$' . $element->render_name();
    }
}