<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

class Display extends QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $question = $complex_content_object_path_node->get_content_object();
        $formvalidator = $this->get_formvalidator();
        
        $renderer = $this->get_renderer();
        
        $min = $question->get_low();
        $max = $question->get_high();
        $question_name = $complex_question->get_id();
        
        for ($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
        }
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);
        
        $formvalidator->addElement(
            'select', 
            $question_name, 
            Translation :: get('Rating') . ': ', 
            $scores, 
            'class="rating_slider"');
        
        if ($answer)
        {
            $formvalidator->setDefaults(array($question_name => $answer[$question_name]));
        }
        
        $renderer->setElementTemplate($element_template, $question_name);
        
        $formvalidator->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->namespaceToFullPath(
                    'Chamilo\Core\Repository\ContentObject\Rating', 
                    true) . 'Resources/Javascript/survey_rating_question.js'));
    }

    function add_borders()
    {
        return true;
    }

    function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();
        
        if ($question->has_instruction())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('ChooseYourRating');
            $instruction[] = '</div>';
        }
        else
        {
            $instruction = array();
        }
        
        return implode(PHP_EOL, $instruction);
    }
}
?>