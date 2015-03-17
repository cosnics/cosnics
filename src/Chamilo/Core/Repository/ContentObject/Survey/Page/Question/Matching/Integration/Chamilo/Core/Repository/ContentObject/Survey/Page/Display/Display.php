<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

class Display extends QuestionDisplay
{

    private $matches;

    private $options;

    private $match_objects;

    private $complex_content_object_path_node;

    private $answer;

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $this->complex_content_object_path_node = $complex_content_object_path_node;
        $this->options = $complex_content_object_path_node->get_content_object()->get_options();
        $this->matches = $complex_content_object_path_node->get_content_object()->get_matches();
        $this->match_objects = array();
        $this->answer = $answer;

        $this->add_matches();
        $this->add_options();
    }

    function add_matches()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();

        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th class="info" >' . Translation :: get('PossibleMatches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));

        $matches = $this->matches;

        $match_label = 'A';
        $index = 0;
        while ($match = $matches->next_result())
        {
            $this->match_objects[] = $match;
            $group = array();
            $group[] = $formvalidator->createElement('static', null, null, $match_label);
            $group[] = $formvalidator->createElement('static', null, null, $match->get_value());
            $formvalidator->addGroup($group, 'match_' . $match_label, null, '', false);

            $renderer->setElementTemplate(
                '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'match_' . $match_label);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'match_' . $match_label);
            $match_label ++;
            $index ++;
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
    }

    function add_options()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();

        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th class="info" colspan="2">' . Translation :: get('ChooseYourOptionMatch') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));

        $question_id = $this->complex_content_object_path_node->get_complex_content_object_item()->get_id();

        $options = $this->options;
        $matches = $this->match_objects;

        $match_options = array();
        $match_label = 'A';
        foreach ($matches as $index => $match)
        {
            $match_options[$match->get_id()] = $match_label;
            $match_label ++;
        }

        $option_count = 0;
        while ($option = $options->next_result())
        {
            $option_id = $option->get_id();
            $option_name = $question_id . '_' . $option_id;

            $group = array();
            $option_number = ($option_count + 1) . '.';
            $group[] = $formvalidator->createElement('static', null, null, $option_number);
            $group[] = $formvalidator->createElement('static', null, null, $option->get_value());
            $group[] = $formvalidator->createElement('select', $option_name, null, $match_options);

            $formvalidator->addGroup($group, 'group_' . $option_name, null, '', false);

            if (isset($this->answer))
            {
                if (isset($this->answer[$option_name]))
                // if (isset($answer[$option_id]))
                {
                    $formvalidator->setDefaults(array($option_name => $this->answer[$option_name]));
                    // $formvalidator->setDefaults(array($option_name => $answer[$option_id]));
                }
            }

            $renderer->setElementTemplate(
                '<tr class="' . ($option_count % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'group_' . $option_name);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'group_' . $option_name);
            $option_count ++;
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
        $formvalidator->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(__NAMESPACE__, true) . 'MatchingQuestionDisplay.js'));
    }
    /*
     * (non-PHPdoc) @see \repository\content_object\survey_page\QuestionDisplay::get_instruction()
     */
    public function get_instruction()
    {
        // TODO Auto-generated method stub
    }
}
?>