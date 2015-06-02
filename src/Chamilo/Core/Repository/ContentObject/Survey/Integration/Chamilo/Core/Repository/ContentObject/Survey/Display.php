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
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="content_object" style="background-image: url(' . $survey->get_icon_path() .
             ');">';
        $html[] = '<div class="title">' . $survey->get_title() . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';
        $html[] = '<div class="description">';
        $html[] = $survey->get_description();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $this->get_formvalidator()->addElement('html', implode(PHP_EOL, $html));
    }

    function get_instruction()
    {
    }
}
?>