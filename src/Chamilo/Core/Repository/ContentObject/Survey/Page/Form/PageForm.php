<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents a form to create or update survey
 */
class PageForm extends ContentObjectForm
{

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        parent::setDefaults($defaults);
    }

    protected function build_creation_form()
    {
        $html_editor_options = array();
        $html_editor_options[FormValidatorHtmlEditorOptions::OPTION_TOOLBAR] = 'RepositorySurveyQuestion';
        
        parent::build_creation_form();
    }
    
    // Inherited
    protected function build_editing_form()
    {
        $html_editor_options = array();
        $html_editor_options[FormValidatorHtmlEditorOptions::OPTION_TOOLBAR] = 'RepositorySurveyQuestion';
        parent::build_editing_form();
    }
    
    // Inherited
    function create_content_object()
    {
        $object = new Page();
        $values = $this->exportValues();
        
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        $this->set_content_object($object);
        return parent::update_content_object();
    }
}
?>