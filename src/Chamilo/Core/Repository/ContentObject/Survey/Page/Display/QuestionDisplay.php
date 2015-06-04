<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Libraries\Architecture\ClassnameUtilities;

abstract class QuestionDisplay extends \Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay
{
    
    function addHeader()
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
        
        $header = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $header);
    }

    function addFooter($formvalidator)
    {
        $formvalidator = $this->formvalidator;
        
        $html[] = '</div>';
        $html[] = '</div >';
        
        $footer = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $footer);
    }

}
?>