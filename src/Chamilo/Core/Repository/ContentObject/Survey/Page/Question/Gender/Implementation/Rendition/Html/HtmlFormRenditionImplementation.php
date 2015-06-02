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
        
        if ($this->getPrefix())
        {
            $questionName = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $questionName = $questionId;
        }
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox" ></th>';
        $table_header[] = '<th class="info" >' . Translation :: get('SelectYourChoice') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        if ($this->getPrefix())
        {
            $questionName = $this->getPrefix() . '_' . $questionId;
        }
        else
        {
            $questionName = $questionId;
        }
        
        $option_name = $questionName;
        $radio_button = $formValidator->createElement('radio', $option_name, null, null, '1');
        $group = array();
        $group[] = $radio_button;
        $group[] = $formValidator->createElement('static', null, null,'vrouw');
        $formValidator->addGroup($group, 'option', null, '', false);
        
        $option_name = $questionName;
        $radio_button = $formValidator->createElement('radio', $option_name, null, null, '2');
        $group = array();
        $group[] = $radio_button;
        $group[] = $formValidator->createElement('static', null, null, 'man');
        $formValidator->addGroup($group, 'option', null, '', false);
        
        $renderer->setElementTemplate(
            '<tr class="' . (1 % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
            'option');
        $renderer->setGroupElementTemplate('<td>{element}</td>', 'option');
        
        $table_footer = array();
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_footer));
        
        return $formValidator;
    }
}