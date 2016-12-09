<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Storage\DataClass\DateTime;
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
        
        $tableHeader = array();
        $tableHeader[] = '<table class="table table-striped table-bordered table-hover table-data take_survey">';
        $tableHeader[] = '<thead>';
        $tableHeader[] = '<tr>';
        
        if ($question->get_question_type() == DateTime::TYPE_DATE)
        {
            $tableHeader[] = '<th class="info" >' . Translation::get('EnterDate') . '</th>';
        }
        elseif ($question->get_question_type() == DateTime::TYPE_TIME)
        {
            $html[] = '<th class="info" >' . Translation::get('EnterTime') . '</th>';
        }
        
        $tableHeader[] = '</tr>';
        $tableHeader[] = '</thead>';
        $tableHeader[] = '<tbody>';
        $tableHeader[] = '<tr>';
        $tableHeader[] = '<td>';
        $formValidator->addElement('html', implode(PHP_EOL, $tableHeader));
        
        $namespace = $question->package();
        
        if ($this->getPrefix())
        {
            $questionName = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $questionName = $questionId;
        }
        
        $attributes = $this->getAttributes();
        
        if ($question->get_question_type() == DateTime::TYPE_DATE)
        {
            $formValidator->add_datepicker($questionName, '', false, $attributes);
        }
        
        if ($question->get_question_type() == DateTime::TYPE_TIME)
        {
            $formValidator->add_timepicker($questionName);
        }
        
        $tableFooter = array();
        $tableFooter[] = '</td>';
        $tableFooter[] = '</tr>';
        $tableFooter[] = '</tbody>';
        $tableFooter[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $tableFooter));
        return $formValidator;
    }
}