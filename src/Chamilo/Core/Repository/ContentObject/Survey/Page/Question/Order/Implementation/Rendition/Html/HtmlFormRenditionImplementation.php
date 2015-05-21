<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass\Order;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Implementation\Rendition\Html\HtmlFormRenditionImplementation
{
    const ORDER_LIMIT = 'order_limit';
    const OPTION_COUNT = 'order_count';
   
    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    function initialize()
    {
        $formValidator = parent :: initialize();
        $displayType = $this->get_content_object()->get_display_type();
        
        if ($displayType == Order :: DISPLAY_TYPE_ONE_COLUMN)
        {
            return $this->initializeOneColumn($formValidator);
        }
        elseif ($displayType == Order :: DISPLAY_TYPE_TWO_COLUMN)
        {
            return $this->initializeTwoColumn($formValidator);
        }
    }
   

    function initializeOneColumn($formValidator)
    {
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        
        $options_count = $question->get_number_of_options();
        $options = $question->get_options();
        
        $questionId = $this->getQuestionId();
        
        $table_height = $options_count * 2.5;
        
        $table_header = array();
        $table_header[] = '<table style="height: ' . $table_height . 'em;" >';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="info">' . Translation :: get('YourRanking') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $html = array();
        
        $html[] = '<tr >';
        $html[] = '<td>';
        $html[] = '<ul id="sortable1" class="connectedSortable" style="height: ' . $table_height . 'em; width: 250px;" >';
        foreach ($options as $option)
        {
            $answer_options[$option->get_id()] = $option->get_value();
            
            $html[] = '<li class="ui-state-default">' . $option->get_value() . '</li>';
        }
        $html[] = '</ul>';
        $html[] = '</td>';
        $html[] = '</tr>';
        
        $formValidator->addElement('html', implode(PHP_EOL, $html));
        
        $table_footer = array();
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_footer));
        
        $script[] = '<script>';
        $script[] = '$(function() {';
        $script[] = '$( "#sortable1, #sortable2" ).sortable({';
        $script[] = '   connectWith: ".connectedSortable"';
        $script[] = '   ,placeholder: "ui-state-highlight"';
        $script[] = ' }).disableSelection();';
        $script[] = '});';
        $script[] = '</script>';
        
        $formValidator->addElement('html', implode(PHP_EOL, $script));
        return $formValidator;
    }

    function initializeTwoColumn($formValidator)
    {
        $renderer = $formValidator->get_renderer();
        $question = $this->get_content_object();
        
        $options_count = $question->get_number_of_options();
        $options = $question->get_options();
        
        $table_height = $options_count * 2.5;
        
        $table_header = array();
        $table_header[] = '<table style="height: ' . $table_height . 'em;" >';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="info">' . Translation :: get('YourChoices') . '</th>';
        $table_header[] = '<th class="info" >' . Translation :: get('YourRanking') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $html = array();
        
        $html[] = '<tr >';
        $html[] = '<td>';
        $html[] = '<div id="options" style="height: ' . $table_height . 'em; width: 250px;" >';
        foreach ($options as $option)
        {
            $html[] = '<div id="order_' . $option->get_id() . '" >' . $option->get_value() . '</div>';
        }
        $html[] = '</div>';
        $html[] = '</td>';
        
        $html[] = '<td>';
        $html[] = '<div id="choices" style="height: ' . $table_height .
             'em; width: 250px; background-color: #B8B8B8; " >';
        $html[] = '</div>';
        $html[] = '</td>';
        
        $html[] = '</tr>';
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $html));
        
        $formValidator->addElement(
            'hidden', 
            self :: ORDER_LIMIT, 
            $question->get_order_limit(), 
            array('id' => self :: ORDER_LIMIT));
        
        $formValidator->addElement('hidden', self :: OPTION_COUNT, $options_count, array('id' => self :: OPTION_COUNT));
        
        $formValidator->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order', 
                    true) . 'TwoColumn.js'));
        return $formValidator;
    }
  
}
?>