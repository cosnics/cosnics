<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Storage\DataClass\DateTime;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Implementation\Rendition\Html\HtmlFormRenditionImplementation
{
     /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function initialize()
    {
        $formValidator = parent :: initialize();
        $renderer = $formValidator->get_renderer();
        $question =  $this->get_content_object();
        $questionId = $this->getQuestionId();
      
        
        $tableHeader = array();
        $tableHeader[] = '<table class="data_table take_survey">';
        $tableHeader[] = '<thead>';
        $tableHeader[] = '<tr>';
        
        if ($question->get_question_type() == DateTime :: TYPE_DATE)
        {
            $tableHeader[] = '<th class="info" >' . Translation :: get('EnterDate') . '</th>';
        }
        elseif ($question->get_question_type() == DateTime :: TYPE_TIME)
        {
            $html[] = '<th class="info" >' . Translation :: get('EnterTime') . '</th>';
        }
        
        $tableHeader[] = '</tr>';
        $tableHeader[] = '</thead>';
        $tableHeader[] = '<tbody>';
        $tableHeader[] = '<tr>';
        $tableHeader[] = '<td>';
        $formValidator->addElement('html', implode(PHP_EOL, $tableHeader));
        
        $namespace = $question->package();
        
        if ($question->get_question_type() == DateTime :: TYPE_DATE)
        {
            $formValidator->add_datepicker($questionId, '', false);
        }
        
        if ($question->get_question_type() == DateTime :: TYPE_TIME)
        {
            $html = array();
            $html[] = '<div id="timepicker_' . $questionId . '" name="' . $questionId .
                 '"></div>';
            $html[] = '<script type="text/javascript" src="' . Path :: getInstance()->getJavascriptPath(
                $namespace, 
                true) . 'Time.js' . '"></script>';
            $formValidator->addElement('html', implode(PHP_EOL, $html));
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