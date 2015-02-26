<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestionOption;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.content_object.match_numeric_question
 */
class AssessmentMatchNumericQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getBasePath(true) .
                     'repository/content_object/match_question/resources/javascript/match_question.js'));
        $this->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getBasePath(true) .
                     'repository/content_object/assessment_match_numeric_question/resources/javascript/match_numeric_question.js'));
        $this->add_options();
        $this->add_example_box();
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getBasePath(true) .
                     'repository/content_object/match_question/resources/javascript/match_question.js'));
        $this->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getBasePath(true) .
                     'repository/content_object/assessment_match_numeric_question/resources/javascript/match_numeric_question.js'));
        $this->add_options();
        $this->add_example_box();
    }

    public function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            $defaults[AssessmentMatchNumericQuestion :: PROPERTY_HINT] = $object->get_hint();
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                foreach ($options as $index => $option)
                {
                    $defaults['option'][$index] = $option->get_value();
                    $defaults['tolerance'][$index] = $option->get_tolerance();
                    $defaults['option_weight'][$index] = $option->get_score();
                    $defaults['comment'][$index] = $option->get_feedback();
                }
                $defaults['tolerance_type'] = $object->get_tolerance_type();
            }
            else
            {
                $number_of_options = intval($_SESSION['match_number_of_options']);
                
                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults['option_weight'][$option_number] = 0;
                    $defaults['tolerance'][$option_number] = 1;
                }
            }
        }
        parent :: setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new AssessmentMatchNumericQuestion();
        $object->set_hint($this->exportValue(AssessmentMatchNumericQuestion :: PROPERTY_HINT));
        $this->set_content_object($object);
        $this->add_options_to_object();
        $result = parent :: create_content_object();
        return $result;
    }

    public function update_content_object()
    {
        $this->get_content_object()->set_hint($this->exportValue(AssessmentMatchNumericQuestion :: PROPERTY_HINT));
        $this->add_options_to_object();
        return parent :: update_content_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $options = array();
        foreach ($values['option'] as $option_id => $value)
        {
            $tolerance = $values['tolerance'][$option_id];
            $score = $values['option_weight'][$option_id];
            $feedback = $values['comment'][$option_id];
            $options[] = new AssessmentMatchNumericQuestionOption($value, $tolerance, $score, $feedback);
        }
        $object->set_tolerance_type($values['tolerance_type']);
        $object->set_options($options);
    }

    public function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
        {
            return false;
        }
        return parent :: validate();
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this match question
     */
    private function add_options()
    {
        $renderer = $this->defaultRenderer();
        
        if (! $this->isSubmitted())
        {
            unset($_SESSION['match_number_of_options']);
            unset($_SESSION['match_skip_options']);
        }
        if (! isset($_SESSION['match_number_of_options']))
        {
            $_SESSION['match_number_of_options'] = 3;
        }
        if (! isset($_SESSION['match_skip_options']))
        {
            $_SESSION['match_skip_options'] = array();
        }
        if (isset($_POST['add']))
        {
            $_SESSION['match_number_of_options'] = $_SESSION['match_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['match_skip_options'][] = $indexes[0];
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['match_number_of_options'] = $object->get_number_of_options();
        }
        $number_of_options = intval($_SESSION['match_number_of_options']);
        
        $this->addElement(
            'hidden', 
            'match_number_of_options', 
            $_SESSION['match_number_of_options'], 
            array('id' => 'match_number_of_options'));
        
        $select_options = array();
        $select_options[AssessmentMatchNumericQuestion :: TOLERANCE_TYPE_ABSOLUTE] = Translation :: get('Absolute');
        $select_options[AssessmentMatchNumericQuestion :: TOLERANCE_TYPE_RELATIVE] = Translation :: get('Relative');
        $select_group = array();
        $select_group[] = & $this->createElement(
            'select', 
            AssessmentMatchNumericQuestion :: PROPERTY_TOLERANCE_TYPE, 
            Translation :: get('ToleranceType'), 
            $select_options);
        
        $this->addElement('category', Translation :: get('Properties'));
        $this->addGroup($select_group, 'tolerance_type', Translation :: get('ToleranceType'), '', false);
        
        $html_editor_options = array();
        $html_editor_options['width'] = '595';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $renderer = $this->defaultRenderer();
        $this->add_html_editor(
            AssessmentMatchNumericQuestion :: PROPERTY_HINT, 
            Translation :: get('Hint', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)), 
            false, 
            $html_editor_options);
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Options'));
        
        $buttons = array();
        // Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when
        // clicking an image button
        $buttons[] = $this->createElement(
            'style_button', 
            'add[]', 
            Translation :: get('AddItem'), 
            array('class' => 'normal add', 'id' => 'add_numeric_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['toolbar'] = 'RepositoryQuestion';
        
        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('PossibleAnswer') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Tolerance') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
        
        $textarea_height = $html_editor_options['height'];
        $textarea_width = $html_editor_options['width'];
        
        if (strpos($textarea_height, '%') === false)
        {
            $textarea_height .= 'px';
        }
        if (strpos($textarea_width, '%') === false)
        {
            $textarea_width .= 'px';
        }
        
        $i = 1;
        
        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['match_skip_options']))
            {
                $group = array();
                
                $group[] = & $this->createElement('static', null, null, $i);
                $group[] = $this->createElement(
                    'textarea', 
                    "option[$option_number]", 
                    Translation :: get('Answer'), 
                    array('style' => 'width: 100%; height:' . $textarea_height));
                $group[] = $this->createElement(
                    'text', 
                    "tolerance[$option_number]", 
                    Translation :: get('Tolerance'), 
                    'size="2"  class="input_numeric"');
                $group[] = $this->create_html_editor(
                    "comment[$option_number]", 
                    Translation :: get('Comment'), 
                    $html_editor_options);
                $group[] = & $this->createElement(
                    'text', 
                    "option_weight[$option_number]", 
                    Translation :: get('Weight'), 
                    'size="2"  class="input_numeric"');
                
                if ($number_of_options - count($_SESSION['match_skip_options']) > 2)
                {
                    $group[] = & $this->createElement(
                        'image', 
                        'remove[' . $option_number . ']', 
                        Theme :: getInstance()->getCommonImagePath() . 'action_delete.png', 
                        array('class' => 'remove_option', 'id' => $option_number));
                }
                else
                {
                    $group[] = & $this->createElement(
                        'static', 
                        null, 
                        null, 
                        '<img src="' . Theme :: getInstance()->getCommonImagePath() .
                             'action_delete_na.png" class="remove_option" />');
                }
                
                $this->addGroup($group, 'option_' . $option_number, null, '', false);
                
                // TODO: we need a new matching type for numeric values with a ,
                $this->addGroupRule(
                    'option_' . $option_number, 
                    array(
                        // "option[$option_number]" => array(
                        // array(Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                        // 'numeric')),
                        // "tolerance[$option_number]" => array(
                        // array(Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                        // 'numeric')),
                        "option_weight[$option_number]" => array(
                            array(
                                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES), 
                                'numeric'))));
                
                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') .
                         '">{element}</tr>', 
                        'option_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);
                
                $i ++;
            }
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));
        
        $renderer->setElementTemplate(
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 
            'question_buttons');
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 
            'question_buttons');
        
        $buttons = array();
        // Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when
        // clicking an image button
        $buttons[] = $this->createElement(
            'style_button', 
            'add[]', 
            Translation :: get('AddItem'), 
            array('class' => 'normal add', 'id' => 'add_numeric_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        $this->addElement('category');
    }
}
