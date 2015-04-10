<?php
namespace Chamilo\Core\Metadata\Value\Element\Form;

use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the schema
 */
class ElementValueForm extends FormValidator
{

    /**
     *
     * @var \Chamilo\Core\Metadata\controlled_vocabulary\storage\data_class\ControlledVocabulary[]
     */
    private $controlled_vocabulary;

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param DefaultElementValue $element_value
     * @param \Chamilo\Core\Metadata\controlled_vocabulary\storage\data_class\ControlledVocabulary[] $controlled_vocabulary
     */
    public function __construct($form_url, $element_value = null, array $controlled_vocabulary = array())
    {
        parent :: __construct('element_value', 'post', $form_url);
        
        $this->controlled_vocabulary = $controlled_vocabulary;
        
        $this->build_form();
        
        if ($element_value && $element_value->is_identified())
        {
            $this->set_defaults($element_value);
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
                DefaultElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID, 
                Translation :: get('Value', null, Utilities :: COMMON_LIBRARIES), 
                $controlled_vocubalary_values);
        }
        else
        {
            $this->addElement(
                'text', 
                DefaultElementValue :: PROPERTY_VALUE, 
                Translation :: get('Value', null, Utilities :: COMMON_LIBRARIES));
            
            $this->addRule(
                DefaultElementValue :: PROPERTY_VALUE, 
                Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 
                'required');
        }
        
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
     * @param DefaultElementValue $element_value
     */
    protected function set_defaults($element_value)
    {
        $defaults = array();
        
        $defaults[DefaultElementValue :: PROPERTY_VALUE] = $element_value->get_value();
        $defaults[DefaultElementValue :: PROPERTY_ELEMENT_VOCABULARY_ID] = $element_value->get_element_vocabulary_id();
        $defaults[DefaultElementValue :: PROPERTY_VALUE] = $element_value->get_value();
        
        $this->setDefaults($defaults);
    }
}