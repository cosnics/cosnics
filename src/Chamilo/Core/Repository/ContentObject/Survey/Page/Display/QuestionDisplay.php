<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

abstract class QuestionDisplay
{

    private $formvalidator;

    private $complex_content_object_path_node;

    private $renderer;

    private $answer;

    function __construct($formvalidator, ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $this->formvalidator = $formvalidator;
        $this->renderer = $formvalidator->defaultRenderer();
        $this->complex_content_object_path_node = $complex_content_object_path_node;
        $this->answer = $answer;
    }

    function get_renderer()
    {
        return $this->renderer;
    }

    function get_formvalidator()
    {
        return $this->formvalidator;
    }

    function run()
    {
        $formvalidator = $this->formvalidator;
        
        $this->add_header();
        $this->process($this->complex_content_object_path_node, $this->answer);
        $this->add_footer();
    }

    abstract function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer);
    
    // abstract function get_instruction();
    function add_header()
    {
        $formvalidator = $this->formvalidator;
        
        $content_object = $this->complex_content_object_path_node->get_content_object();
        $class_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($content_object);
        $complex_content_object_item = $this->complex_content_object_path_node->get_complex_content_object_item();
        if (! $complex_content_object_item->is_visible())
        {
            $html[] = '<div  class="question ' . $class_name . '" id="' . $complex_content_object_item->get_id() .
                 '" style="display: none;">';
        }
        else
        {
            $html[] = '<div  class="question ' . $class_name . '" id="' . $complex_content_object_item->get_id() . '">';
        }
        
        $html[] = '<a name=' . $complex_content_object_item->get_id() . '></a>';
        
        if ($this->complex_content_object_path_node->is_question())
        {
            $html[] = '<div class="title">';
            $html[] = '<div class="number">';
            $html[] = '<div class="bevel">';
            $html[] = $this->complex_content_object_path_node->get_question_nr() . '.';
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '<div class="text">';
            $html[] = '<div class="bevel">';
            $html[] = $title = $content_object->get_question();
            $html[] = '<div class="onoff">';
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '<div class="answer">';
            $html[] = '<div class="clear"></div>';
        }
        else
        {
            
            $html[] = '<div class="survey">';
            $html[] = '<div class="clear"></div>';
        }
        
        $header = implode("\n", $html);
        $formvalidator->addElement('html', $header);
    }

    function add_footer($formvalidator)
    {
        $formvalidator = $this->formvalidator;
        
        $html[] = '</div>';
        $html[] = '</div>';
        
        $footer = implode("\n", $html);
        $formvalidator->addElement('html', $footer);
    }

    static function factory($formvalidator, ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $content_object = $complex_content_object_path_node->get_content_object();
        
        $class = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($content_object->get_type()) .
             '\Display\Display';
        $question_display = new $class($formvalidator, $complex_content_object_path_node, $answer);
        
        return $question_display;
    }
}
?>