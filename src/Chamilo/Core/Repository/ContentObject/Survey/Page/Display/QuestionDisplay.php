<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;

abstract class QuestionDisplay extends \Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay
{

    function addHeader()
    {
        $formvalidator = $this->formvalidator;
        
        $content_object = $this->complex_content_object_path_node->get_content_object();
        $nodeId = $this->complex_content_object_path_node->get_id();
        $class_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($content_object);
        $complex_content_object_item = $this->complex_content_object_path_node->get_complex_content_object_item();
        $complexContentObjectItemId = $complex_content_object_item->get_id();
        
        $visible = $this->complex_content_object_path_node->isVisible($this->getAnswerService());
        
        if (! $visible)
        {
            $html[] = '<div  class="question ' . $class_name . '" id="' . $nodeId . '" data-node_id="' . $nodeId .
                 '" data-complex_question_id="' . $complexContentObjectItemId . '" style="display: none;">';
        }
        else
        {
            $html[] = '<div  class="question ' . $class_name . '" id="' . $nodeId . '" data-node_id="' . $nodeId .
                 '" data-complex_question_id="' . $complexContentObjectItemId . '"   >';
        }
        
        $header = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $header);
    }

    function addFooter($formvalidator)
    {
        $formvalidator = $this->formvalidator;
        
        $html[] = '</div>';
        $html[] = '</div >';
        $html[] = $this->addJavascript();
        
        $footer = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $footer);
    }

    private function addJavascript()
    {
        $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromObject($this);
        return ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath($namespace, true) .
                 'ProcessAnswer.js');
    }
}
?>