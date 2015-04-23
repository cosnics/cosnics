<?php
namespace Chamilo\Core\MetadataOld\ControlledVocabulary\Form;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the controlled vocabulary
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ControlledVocabularyForm extends FormValidator
{

    /**
     * Constructor
     * 
     * @param string $form_url
     * @param ControlledVocabulary $controlled_vocabulary
     */
    public function __construct($form_url, $controlled_vocabulary = null)
    {
        parent :: __construct('controlled_vocabulary', 'post', $form_url);
        
        $this->build_form();
        
        if ($controlled_vocabulary && $controlled_vocabulary->is_identified())
        {
            $this->set_defaults($controlled_vocabulary);
        }
    }

    /**
     * Builds this form
     */
    protected function build_form()
    {
        $this->addElement(
            'text', 
            ControlledVocabulary :: PROPERTY_VALUE, 
            Translation :: get('Value'), 
            array("size" => "50"));
        
        $this->addRule(
            ControlledVocabulary :: PROPERTY_VALUE, 
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
     * @param ControlledVocabulary $controlled_vocabulary
     */
    protected function set_defaults($controlled_vocabulary)
    {
        $defaults = array();
        
        $defaults[ControlledVocabulary :: PROPERTY_VALUE] = $controlled_vocabulary->get_value();
        
        $this->setDefaults($defaults);
    }
}