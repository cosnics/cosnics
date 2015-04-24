<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class represents a form to create or update survey
 */
class SurveyForm extends ContentObjectForm
{

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[Survey :: PROPERTY_PROGRESS_BAR] = $object->get_progress_bar();
            $defaults[Survey :: PROPERTY_MENU] = $object->get_menu();
            $defaults[Survey :: PROPERTY_FINISH_TEXT] = $object->get_finish_text();
        }
        
        parent :: setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        $html_editor_options = array();
        $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR] = 'RepositorySurveyQuestion';
        
        parent :: build_creation_form();
        
        $this->addElement('category', Translation :: get('Properties'));
        $checkbox = $this->createElement(
            'checkbox', 
            Survey :: PROPERTY_MENU, 
            Translation :: get('WithMenu'), 
            '', 
            array());
        $this->addElement($checkbox);
        $checkbox = $this->createElement(
            'checkbox', 
            Survey :: PROPERTY_PROGRESS_BAR, 
            Translation :: get('WithProgressBar'), 
            '', 
            array());
        $this->addElement($checkbox);
        $this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyFinishText'), false);
        $this->addElement('category');
    }
    
    // Inherited
    protected function build_editing_form()
    {
        $html_editor_options = array();
        $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR] = 'RepositorySurveyQuestion';
        
        parent :: build_editing_form();
        
        $this->addElement('category', Translation :: get('Properties'));
        $checkbox = $this->createElement(
            'checkbox', 
            Survey :: PROPERTY_MENU, 
            Translation :: get('WithMenu'), 
            '', 
            array());
        $this->addElement($checkbox);
        $checkbox = $this->createElement(
            'checkbox', 
            Survey :: PROPERTY_PROGRESS_BAR, 
            Translation :: get('WithProgressBar'), 
            '', 
            array());
        $this->addElement($checkbox);
        $this->add_html_editor(Survey :: PROPERTY_FINISH_TEXT, Translation :: get('SurveyFinishText'), false);
        $this->addElement('category');
    }
    
    // Inherited
    function create_content_object()
    {
        $object = new Survey();
        $values = $this->exportValues();
        
        $object->set_progress_bar($values[Survey :: PROPERTY_PROGRESS_BAR]);
        $object->set_menu($values[Survey :: PROPERTY_MENU]);
        $object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        $object->set_progress_bar($values[Survey :: PROPERTY_PROGRESS_BAR]);
        $object->set_finish_text($values[Survey :: PROPERTY_FINISH_TEXT]);
        $object->set_menu($values[Survey :: PROPERTY_MENU]);
        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
?>