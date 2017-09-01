<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: ordering_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
class Display extends QuestionDisplay
{

    public function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();
        
        if ($clo_question->get_random())
        {
            $answers = $this->shuffle_with_keys($question->get_options());
        }
        else
        {
            $answers = $question->get_options();
        }
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th></th>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $question_id = $clo_question->get_id();
        $order_options = $this->get_order_options();
        
        foreach ($answers as $i => $answer)
        {
            $group = array();
            $answer_name = $question_id . '_' . ($i + 1);
            $group[] = $formvalidator->createElement('select', $answer_name, null, $order_options);
            
            $object_renderer = new ContentObjectResourceRenderer(
                $this->get_formvalidator()->get_assessment_viewer(), 
                $answer->get_value());
            
            $group[] = $formvalidator->createElement('static', null, null, $object_renderer->run());
            
            // $formvalidator->addGroup($group, 'option_' . $i, null, '', false);
            $formvalidator->addGroup($group, 'option_' . $question_id . '_' . $i, null, '', false);
            
            // $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') .
            // '">{element}</tr>', 'option_' . $i);
            // $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
            
            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
                'option_' . $question_id . '_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $question_id . '_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
        
        $formvalidator->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(Assessment::package(), true) . 'GiveHint.js'));
    }

    public function add_border()
    {
        return false;
    }

    public function get_instruction()
    {
        if ($this->get_question()->has_description())
        {
            $title = Translation::get('PutAnswersCorrectOrder');
        }
        else
        {
            $title = '';
        }
        
        return $title;
    }

    public function get_order_options()
    {
        $answer_count = count($this->get_question()->get_options());
        
        $options = array();
        $options[- 1] = Translation::get('MakeASelection');
        for ($i = 1; $i <= $answer_count; $i ++)
        {
            $options[$i] = $i;
        }
        
        return $options;
    }

    public function add_footer()
    {
        $formvalidator = $this->get_formvalidator();
        
        if ($this->get_question()->has_hint() && $this->get_configuration()->allow_hints())
        {
            $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();

            $html[] = '<div class="panel-body">';
            $html[] = '<div class="splitter">' . Translation :: get('Hint') . '</div>';
            $html[] = '<div class="with_borders"><a id="' . $hint_name .
                 '" class="btn btn-default hint_button"><span class="glyphicon glyphicon-gift"></span> ' .
                 Translation :: get('GetAHint') . '</a></div>';
            $html[] = '</div>';

            $footer = implode(PHP_EOL, $html);
            $formvalidator->addElement('html', $footer);
        }
        
        parent::add_footer();
    }
}
