<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Common\Rendition\Html\HtmlFormRenditionImplementation
{

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    function initialize()
    {
        $formValidator = parent::initialize();
        $displayType = $this->get_content_object()->get_display_type();
        
        if ($displayType == MultipleChoice::DISPLAY_TYPE_SELECT)
        {
            return $this->initializeSelect($formValidator);
        }
        elseif ($displayType == MultipleChoice::DISPLAY_TYPE_TABLE)
        {
            return $this->initializeTable($formValidator);
        }
    }

    function initializeSelect($formValidator)
    {
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        $options = $question->get_options();
        $type = $question->get_answer_type();
        
        $answer_options = array();
        
        foreach ($options as $option)
        {
            $answer_options[$option->get_id()] = $option->get_value();
        }
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);
        
        $questionId = $this->getQuestionId();
        
        if ($this->getPrefix())
        {
            $questionName = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $questionName = $questionId;
        }
        
        if ($type == MultipleChoice::ANSWER_TYPE_CHECKBOX)
        {
            $advanced_select = $formValidator->createElement(
                'select', 
                $questionName, 
                '', 
                $answer_options, 
                array(
                    'multiple' => 'true', 
                    'class' => 'advanced_select_question', 
                    'size' => (count($answer_options) > 10 ? 10 : count($answer_options))));
            $formValidator->addElement($advanced_select);
        }
        else
        {
            $select_box = $formValidator->createElement(
                'select', 
                $questionName, 
                '', 
                $answer_options, 
                'class="select_question"');
            $formValidator->addElement($select_box);
        }
        
        $renderer->setElementTemplate($element_template, $questionName);
        return $formValidator;
    }

    function initializeTable($formValidator)
    {
        $renderer = $formValidator->defaultRenderer();
        $question = $this->get_content_object();
        $options = $question->get_options();
        $type = $question->get_answer_type();
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>';
        $table_header[] = '<th class="info" >' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $questionId = $this->getQuestionId();
        
        if ($this->getPrefix())
        {
            $questionName = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $questionName = $questionId;
        }
        
        $attributes = $this->getAttributes();
        
        foreach ($options as $option)
        {
            $i = $option->get_id();
            $group = array();
            
            if ($type == MultipleChoice::ANSWER_TYPE_RADIO)
            {
                $option_name = $questionName;
                $radio_button = $formValidator->createElement('radio', $option_name, null, null, $i, $attributes);
                $group[] = $radio_button;
                $group[] = $formValidator->createElement('static', null, null, $option->get_value());
            }
            elseif ($type == MultipleChoice::ANSWER_TYPE_CHECKBOX)
            {
                $option_name = $questionName . '_' . $i;
                $check_box = $formValidator->createElement('checkbox', $option_name, null, null, $attributes, $i);
                $group[] = $check_box;
                $group[] = $formValidator->createElement('static', null, null, $option->get_value());
            }
            
            $formValidator->addGroup($group, 'option_' . $i, null, '', false);
            
            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
                'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
        }
        
        $table_footer = array();
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_footer));
        return $formValidator;
    }

    function get_instruction()
    {
        $type = $this->get_content_object()->get_answer_type();
        
        if ($type == MultipleChoice::ANSWER_TYPE_RADIO && $this->get_content_object()->has_instruction())
        {
            $title = Translation::get('SelectYourChoice');
        }
        elseif ($type == MultipleChoice::ANSWER_TYPE_CHECKBOX && $this->get_content_object()->has_instruction())
        {
            $title = Translation::get('SelectYourChoices');
        }
        else
        {
            $title = '';
        }
        
        return $title;
    }
}
?>