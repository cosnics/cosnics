<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;

abstract class PageDisplay
{

    private $formvalidator;

    private $complex_content_object_path_node;

    private $renderer;

    function __construct($formvalidator, ComplexContentObjectPathNode $complex_content_object_path_node)
    {
        $this->formvalidator = $formvalidator;
        $this->renderer = $formvalidator->defaultRenderer();
        $this->complex_content_object_path_node = $complex_content_object_path_node;
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
        $this->process($this->complex_content_object_path_node);
        $this->add_footer();
    }

    abstract function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer);

    abstract function get_instruction();

    function add_header()
    {
        $formvalidator = $this->formvalidator;
        $html = array();
        $header = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $header);
    }

    function add_footer($formvalidator)
    {
        $formvalidator = $this->formvalidator;
        $html = array();
        $footer = implode(PHP_EOL, $html);
        $formvalidator->addElement('html', $footer);
    }

    static function factory($formvalidator, ComplexContentObjectPathNode $complex_content_object_path_node)
    {
        $content_object = $complex_content_object_path_node->get_content_object();
        $package = $content_object->package();
        $class = $package.'\Integration\Chamilo\Core\Repository\ContentObject\Survey\Display\Display';
        $page_display = new $class($formvalidator, $complex_content_object_path_node);
        
        return $page_display;
    }
}
?>