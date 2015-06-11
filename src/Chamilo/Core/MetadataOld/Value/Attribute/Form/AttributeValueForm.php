<?php
namespace Chamilo\Core\MetadataOld\Value\Attribute\Form;

use Chamilo\Core\MetadataOld\Value\Attribute\Storage\DataClass\DefaultAttributeValue;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the schema
 */
class AttributeValueForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary[]
     */
    private $controlled_vocabulary;

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param DefaultAttributeValue $attribute_value
     * @param \Chamilo\Core\MetadataOld\controlled_vocabulary\storage\data_class\ControlledVocabulary[] $controlled_vocabulary
     */
    public function __construct($form_url, $attribute_value = null, array $controlled_vocabulary = array())
    {
        parent :: __construct('attribute_value', 'post', $form_url);
        
        $this->controlled_vocabulary = $controlled_vocabulary;
        
        $this->build_form();
        
        if ($attribute_value && $attribute_value->is_identified())
        {
            $this->set_defaults($attribute_value);
        }
    }

    /**
     * Builds this form
     */
    protected function build_form()
    {
        if (count($this->controlled_vocabulary))
        {
            $controlled_vocubalary_values = array();
            
            foreach ($this->controlled_vocabulary as $controlled_vocubalary)
            {
                $controlled_vocubalary_values[$controlled_vocubalary->get_id()] = $controlled_vocubalary->get_value();
            }
            
            $this->addElement(
                'select', 
                DefaultAttributeValue :: PROPERTY_ATTRIBUTE_VOCABULARY_ID, 
                Translation :: get('Value', null, Utilities :: COMMON_LIBRARIES), 
                $controlled_vocubalary_values);
        }
        else
        {
            $this->addElement(
                'text', 
                DefaultAttributeValue :: PROPERTY_VALUE, 
                Translation :: get('Value', null, Utilities :: COMMON_LIBRARIES));
            
            $this->addRule(
                DefaultAttributeValue :: PROPERTY_VALUE, 
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                'required');
        }
        
        $this->addRule(
            DefaultAttributeValue :: PROPERTY_VALUE, 
            Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
            'required');
        
        $buttons[] = $this->createElement(
            'style_submit_button', 
            'submit', 
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'positive'));
        
        $buttons[] = $this->createElement(
            'style_reset_button', 
            'reset', 
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), 
            array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets the default values
     * 
     * @param DefaultAttributeValue $attribute_value
     */
    protected function set_defaults($attribute_value)
    {
        $defaults = array();
        
        $defaults[DefaultAttributeValue :: PROPERTY_VALUE] = $attribute_value->get_value();
        $defaults[DefaultAttributeValue :: PROPERTY_ELEMENT_VOCABULARY_ID] = $attribute_value->get_element_vocabulary_id();
        $defaults[DefaultAttributeValue :: PROPERTY_VALUE] = $attribute_value->get_value();
        
        $this->setDefaults($defaults);
    }
}