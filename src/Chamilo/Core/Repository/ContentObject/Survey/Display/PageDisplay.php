<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;

abstract class PageDisplay
{

    protected  $formvalidator;

    protected $complex_content_object_path_node;

    protected $renderer;
    
    protected $answer;

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
        
        $this->addHeader();
        $this->process($this->complex_content_object_path_node, $this->answer);
        $this->addFooter();
    }

    abstract function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer);

    function addHeader()
    {
        $formvalidator = $this->formvalidator;
        $html = array();
        $header = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $header);
    }

    function addFooter($formvalidator)
    {
        $formvalidator = $this->formvalidator;
        $html = array();
        $footer = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $footer);
    }

    static function factory($formvalidator, ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $content_object = $complex_content_object_path_node->get_content_object();
        $package = $content_object->package();
        
        $class = $package . '\Integration\Chamilo\Core\Repository\ContentObject\Survey\Display';
        
        if (class_exists($class))
        {
            $display = new $class($formvalidator, $complex_content_object_path_node, $answer);
        }
        else
        {
            throw new ClassNotExistException($class);
        }
        
        return $display;
    }
}
?>