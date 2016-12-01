<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Common\Rendition\Html;

use Chamilo\Libraries\Platform\Translation;

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
        $formValidator = parent::initialize();
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        
        $questionId = $this->getQuestionId();
        $options = $question->getOptions();
        
        $table_header = array();
        $table_header[] = '<div >';
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>';
        $table_header[] = '<th class="info" >' . Translation::get('SelectYourChoice') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $attributes = $this->getAttributes();
        
        if ($this->getPrefix())
        {
            $option_name = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $option_name = $questionId;
        }
        
        foreach ($options as $i => $option)
        {
            $group = array();
            
            $radio = $formValidator->createElement('radio', $option_name, null, null, $i, $attributes);
            
            $group[] = $radio;
            
            $group[] = $formValidator->createElement(
                'static', 
                null, 
                null, 
                '<div style="text-align: left;">' . $option . '</div>');
            
            $formValidator->addGroup($group, 'choice_option_' . $i, null, '', false);
            
            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
                'choice_option_' . $i);
            $renderer->setGroupElementTemplate('<td style="text-align: center;">{element}</td>', 'choice_option_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $table_footer[] = '</div>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_footer));
        
        return $formValidator;
    }
}