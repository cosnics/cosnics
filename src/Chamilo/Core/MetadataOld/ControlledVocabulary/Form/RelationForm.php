<?php
namespace Chamilo\Core\MetadataOld\ControlledVocabulary\Form;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Manager;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementType;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Form for the controlled vocabulary
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RelationForm extends FormValidator
{

    /**
     * Constructor
     * 
     * @param string $form_url
     *
     * @param ControlledVocabulary[] $controlled_vocabularies
     */
    public function __construct($form_url, $controlled_vocabularies = array())
    {
        parent :: __construct('controlled_vocabulary_relation', 'post', $form_url);
        
        $this->build_form();
        
        if (count($controlled_vocabularies) && ! $this->validate())
        {
            $this->set_defaults($controlled_vocabularies);
        }
    }

    /**
     * Builds this form
     */
    protected function build_form()
    {
        $types = new AdvancedElementFinderElementTypes();
        
        $types->add_element_type(
            new AdvancedElementFinderElementType(
                'controlled_vocabulary', 
                Translation :: get('ControlledVocabulary'), 
                __NAMESPACE__, 
                'controlled_vocabulary_feed'));
        
        $this->addElement('advanced_element_finder', Manager :: PARAM_CONTROLLED_VOCABULARY_ID, null, $types);
        
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
     * @param ControlledVocabulary[] $controlled_vocabularies
     */
    protected function set_defaults($controlled_vocabularies = array())
    {
        $default_elements = new AdvancedElementFinderElements();
        
        foreach ($controlled_vocabularies as $controlled_vocabulary)
        {
            $default_elements->add_element(
                new AdvancedElementFinderElement(
                    'controlled_vocabulary_id_' . $controlled_vocabulary->get_id(), 
                    'type', 
                    $controlled_vocabulary->get_value(), 
                    $controlled_vocabulary->get_value()));
        }
        
        $element = $this->getElement(Manager :: PARAM_CONTROLLED_VOCABULARY_ID);
        $element->setDefaultValues($default_elements);
    }
}