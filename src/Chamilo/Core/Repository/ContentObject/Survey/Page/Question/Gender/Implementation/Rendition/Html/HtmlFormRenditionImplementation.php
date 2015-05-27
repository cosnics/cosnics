<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Implementation\Rendition\Html;

use Chamilo\Libraries\Platform\Translation;

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
        $questionId = $this->getQuestionId();
        
        $html = array();
        $html[] = '<table class="data_table take_survey">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox" ></th>';
        $html[] = '<th class="info" >' . Translation :: get('SelectYourChoice') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        
        $html[] = '<tr class="row_even">';
        $html[] = '<td><input type="radio" value="0" name="' . $questionId . '"/></td>';
        $html[] = '<td>' . Translation :: get('Male') . '</td>';
        $html[] = '</tr>';
        
        $html[] = '<tr class="row_odd">';
        $html[] = '<td><input type="radio" value="1" name="' . $questionId . '"/></td>';
        $html[] = '<td>' . Translation :: get('Female') . '</td>';
        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $html));
        return $formValidator;
    }
  
}