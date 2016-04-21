<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Common\Rendition\Html;

use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;

/**
 *
 * @package repository.content_object.survey_matrix_question
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
    public function initialize()
    {
        $formValidator = parent :: initialize();
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        $questionId = $this->getQuestionId();
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="info" >' . $this->get_instruction($question) . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $table_header[] = '<tr>';
        $table_header[] = '<td>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $this->add_html_editor($question, $formValidator);
        
        $table_footer[] = '</td>';
        $table_footer[] = '</tr>';
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_footer));
        return $formValidator;
    }

    function add_html_editor($question, $formValidator)
    {
        $html_editor_options = array();
        $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_WIDTH] = '100%';
        $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_TOOLBAR] = 'Assessment';
        $html_editor_options[FormValidatorHtmlEditorOptions :: OPTION_COLLAPSE_TOOLBAR] = true;
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);
        $renderer = $formValidator->get_renderer();
        
        $questionId = $this->getQuestionId();
        
        if ($this->getPrefix())
        {
            $questionName = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $questionName = $questionId;
        }
        
        $formValidator->add_html_editor($questionName, '', false, $html_editor_options);
        
        $renderer->setElementTemplate($element_template, $questionName);
    }

    function get_instruction($question)
    {
        if ($question->has_instruction())
        {
            $instruction = 'test';
        }
        else
        {
            $instruction = '';
        }
        
        return $instruction;
    }
}