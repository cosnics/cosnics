<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Implementation\Rendition\Html\HtmlFormRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass\ComplexOrder;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_order_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormOneColumnRenditionImplementation extends HtmlFormRenditionImplementation
{

    private $question;

    function render(FormValidator $formvalidator, ComplexOrder $complex_content_object_item, 
        $answer = null)
    {
        // $formvalidator = $this->get_context()->get_formvalidator();
        $renderer = $formvalidator->get_renderer();
        // $complex_question = $this->get_context()->get_complex_content_object_item();
        $this->question = $this->get_content_object();
        
        $options_count = $this->question->get_number_of_options();
        $options = $this->question->get_options();
        
        $question_id = $this->question->get_id();
        
        $table_height = $options_count * 2.5;
        
        $table_header = array();
        $table_header[] = '<table style="height: ' . $table_height . 'em;" >';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="info">' . Translation :: get('YourRanking') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));
        
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
        
        $formvalidator->addElement('html', implode(PHP_EOL, $html));
        
        $table_footer = array();
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
        
        $script[] = '<script>';
        $script[] = '$(function() {';
        $script[] = '$( "#sortable1, #sortable2" ).sortable({';
        $script[] = '   connectWith: ".connectedSortable"';
        $script[] = '   ,placeholder: "ui-state-highlight"';
        $script[] = ' }).disableSelection();';
        $script[] = '});';
        $script[] = '</script>';
        
        $formvalidator->addElement('html', implode(PHP_EOL, $script));
    }

    function get_instruction()
    {
        $instruction = array();
        $instruction[] = '<div class="splitter">';
        $instruction[] = Translation :: get('YourRanking');
        $instruction[] = '</div>';
        
        return implode(PHP_EOL, $instruction);
    }
}
?>