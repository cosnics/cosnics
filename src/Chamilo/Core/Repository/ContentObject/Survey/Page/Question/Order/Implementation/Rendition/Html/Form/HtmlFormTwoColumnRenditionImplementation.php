<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Implementation\Rendition\Html\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Form\ComplexOrderForm;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Implementation\Rendition\Html\HtmlFormRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_order_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormTwoColumnRenditionImplementation extends HtmlFormRenditionImplementation
{
    const ORDER_LIMIT = 'order_limit';
    const OPTION_COUNT = 'option_count';

    private $question;

    function render(FormValidator $formvalidator, ComplexOrderForm $complex_content_object_item, $answer = null)
    {
        $renderer = $formvalidator->get_renderer();
        $this->question = $this->get_content_object();

        $options_count = $this->question->get_number_of_options();
        $options = $this->question->get_options();

        $question_id = $this->question->get_id();

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
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));

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
        $formvalidator->addElement('html', implode(PHP_EOL, $html));

        $formvalidator->addElement(
            'hidden',
            self :: ORDER_LIMIT,
            $this->question->get_order_limit(),
            array('id' => self :: ORDER_LIMIT));

        $formvalidator->addElement('hidden', self :: OPTION_COUNT, $options_count, array('id' => self :: OPTION_COUNT));

        $formvalidator->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order',
                    true) . 'TwoColumn.js'));
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