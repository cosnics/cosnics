<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Repository\ContentObject\Survey;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;

class Display extends PageDisplay
{

    function process(ComplexContentObjectPathNode $complexContentObjectPathNode, AnswerServiceInterface $answer)
    {
        $survey = $complexContentObjectPathNode->get_content_object();
        
        $html = array();
        
        $html[] = '<div class="panel panel-default">';
        
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $survey->get_icon_image() . ' ' . $survey->get_description();
        $html[] = '</h3>';
        $html[] = '</div>';
        
        $html[] = '<div class="panel-body">';
        $html[] = $survey->get_description();
        $html[] = '</div>';
        
        $html[] = '</div>';
        
        $this->get_formvalidator()->addElement('html', implode(PHP_EOL, $html));
    }

    function get_instruction()
    {
    }
}