<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_open_question_form.class.php$ $
 * 
 * @package repository.lib.content_object.assessment_open_question
 */
/**
 * This class represents a form to create or update open questions
 */
class AssessmentOpenQuestionForm extends ContentObjectForm
{

    public function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        if ($object->get_id() != null)
        {
            $defaults[AssessmentOpenQuestion :: PROPERTY_HINT] = $object->get_hint();
            $defaults[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
            $defaults[AssessmentOpenQuestion :: PROPERTY_FEEDBACK] = $object->get_feedback();
        }
        else
        {
            $defaults[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE] = AssessmentOpenQuestion :: TYPE_OPEN;
        }
        
        parent :: setDefaults($defaults);
    }

    public function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        $types = AssessmentOpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement(
                'radio', 
                AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE, 
                '', 
                $type_label, 
                $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '', false);
        
        $html_editor_options = array();
        $html_editor_options['width'] = '595';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $this->add_html_editor(
            AssessmentOpenQuestion :: PROPERTY_FEEDBACK, 
            Translation :: get('Feedback'), 
            false, 
            $html_editor_options);
        $this->add_html_editor(
            AssessmentOpenQuestion :: PROPERTY_HINT, 
            Translation :: get('Hint', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)), 
            false, 
            $html_editor_options);
        
        $this->addElement('category');
        
        $this->add_example_box();
    }
    
    // Inherited
    public function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
        $types = AssessmentOpenQuestion :: get_types();
        $choices = array();
        foreach ($types as $type_id => $type_label)
        {
            $choices[] = $this->createElement(
                'radio', 
                AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE, 
                '', 
                $type_label, 
                $type_id);
        }
        $this->addGroup($choices, null, Translation :: get('OpenQuestionQuestionType'), '', false);
        
        $html_editor_options = array();
        $html_editor_options['width'] = '595';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $this->add_html_editor(
            AssessmentOpenQuestion :: PROPERTY_FEEDBACK, 
            Translation :: get('Feedback'), 
            false, 
            $html_editor_options);
        $this->add_html_editor(
            AssessmentOpenQuestion :: PROPERTY_HINT, 
            Translation :: get('Hint', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)), 
            false, 
            $html_editor_options);
        
        $this->addElement('category');
        
        $this->add_example_box();
    }
    
    // Inherited
    public function create_content_object()
    {
        $object = new AssessmentOpenQuestion();
        
        $values = $this->exportValues();
        $object->set_hint($values[AssessmentOpenQuestion :: PROPERTY_HINT]);
        $object->set_question_type($values[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE]);
        $object->set_feedback($values[AssessmentOpenQuestion :: PROPERTY_FEEDBACK]);
        
        $this->set_content_object($object);
        return parent :: create_content_object($object);
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        
        $values = $this->exportValues();
        $object->set_hint($values[AssessmentOpenQuestion :: PROPERTY_HINT]);
        $object->set_question_type($values[AssessmentOpenQuestion :: PROPERTY_QUESTION_TYPE]);
        $object->set_feedback($values[AssessmentOpenQuestion :: PROPERTY_FEEDBACK]);
        
        $this->set_content_object($object);
        return parent :: update_content_object();
    }
}
