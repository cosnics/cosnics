<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

abstract class QuestionDisplay extends \Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay
{

    function addHeader()
    {
        $formvalidator = $this->formvalidator;
        
        $content_object = $this->complex_content_object_path_node->get_content_object();
        
        $attributes = $this->complex_content_object_path_node->getDataAttributes();
        $dataAttributeString = " ";
        foreach ($attributes as $key => $value)
        {
            $dataAttributeString = $dataAttributeString . $key . '="' . $value . '" ';
        }
        
        $class_name = ClassnameUtilities::getInstance()->getClassnameFromObject($content_object);
        
        $visible = $this->complex_content_object_path_node->isVisible($this->getAnswerService());
        
        if (! $visible)
        {
            $html[] = '<div  class="question ' . $class_name . '" ' . $dataAttributeString . '" style="display: none;">';
        }
        else
        {
            $html[] = '<div  class="question ' . $class_name . '" ' . $dataAttributeString . '>';
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
        $namespace = ClassnameUtilities::getInstance()->getNamespaceFromObject($this);
        return ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath($namespace, true) . 'ProcessAnswer.js');
    }
}
?>