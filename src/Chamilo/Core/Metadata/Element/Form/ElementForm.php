<?php
namespace Chamilo\Core\Metadata\Element\Form;

use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\Schema;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the element
 */
class ElementForm extends FormValidator
{

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param Element $element
     */
    public function __construct($form_url, Element $element)
    {
        parent::__construct('element', 'post', $form_url);

        $this->element = $element;
        $this->build_form();
        
        if ($this->element->is_identified())
        {
            $this->set_defaults();
        }
    }

    /**
     * Builds this form
     */
    protected function build_form()
    {
        $schema = \Chamilo\Core\Metadata\Storage\DataManager::retrieve_by_id(
            Schema::class_name(), 
            $this->element->get_schema_id());
        $schemaName = $schema->get_namespace() . ' - ' . $schema->get_name();
        
        $this->addElement('static', null, Translation::get('Prefix', null, 'Chamilo\Core\Metadata'), $schemaName);

        //$this->addRule(
          //  Element::PROPERTY_SCHEMA_ID,
           // Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
           // 'required');
        
        $this->addElement(
            'text', 
            Element::PROPERTY_NAME, 
            Translation::get('Name', null, Utilities::COMMON_LIBRARIES));
        $this->addRule(
            Element::PROPERTY_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'text', 
            Element::PROPERTY_DISPLAY_NAME, 
            Translation::get('DisplayName', null, 'Chamilo\Core\Metadata'));
        $this->addRule(
            Element::PROPERTY_DISPLAY_NAME, 
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
            'required');
        
        $this->addElement(
            'radio', 
            Element::PROPERTY_VALUE_TYPE, 
            Translation::get('ValueType'), 
            Translation::get('ValueTypePredefined'), 
            Element::VALUE_TYPE_VOCABULARY_PREDEFINED);
        
        $this->addElement(
            'radio', 
            Element::PROPERTY_VALUE_TYPE, 
            null, 
            Translation::get('ValueTypeUser'), 
            Element::VALUE_TYPE_VOCABULARY_USER);
        
        $this->addElement(
            'radio', 
            Element::PROPERTY_VALUE_TYPE, 
            null, 
            Translation::get('ValueTypeBoth'), 
            Element::VALUE_TYPE_VOCABULARY_BOTH);
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation::get('Save', null, Utilities::COMMON_LIBRARIES));
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation::get('Reset', null, Utilities::COMMON_LIBRARIES));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets the default values
     * 
     * @param Element $element
     */
    protected function set_defaults()
    {
        $defaults = array();
        
        $defaults[Element::PROPERTY_SCHEMA_ID] = $this->element->get_schema_id();
        $defaults[Element::PROPERTY_NAME] = $this->element->get_name();
        $defaults[Element::PROPERTY_DISPLAY_NAME] = $this->element->get_display_name();
        $defaults[Element::PROPERTY_VALUE_TYPE] = $this->element->get_value_type();
        
        $this->setDefaults($defaults);
    }
}