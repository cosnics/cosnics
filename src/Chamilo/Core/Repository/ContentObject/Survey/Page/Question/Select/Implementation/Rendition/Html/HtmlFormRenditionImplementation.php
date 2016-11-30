<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Common\Rendition\Html;

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
        
        $question = $this->get_content_object();
        
        $options = $question->get_options();
        
        $type = $question->get_answer_type();
        
        while ($option = $options->next_result())
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
        
        if ($type == 'checkbox')
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
        
        $formValidator->get_renderer()->setElementTemplate($element_template, $questionName);
        return $formValidator;
    }
}
?>