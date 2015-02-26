<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;

class Display extends QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $question = $complex_content_object_path_node->get_content_object();
        $formvalidator = $this->get_formvalidator();
        
        if ($question->has_description())
        {
            $html[] = '<div class="information">';
            $html[] = $question->get_description();
            
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
            $html[] = '<div class="clear">&nbsp;</div>';
        }
        
        $detail = implode("\n", $html);
        $formvalidator->addElement('html', $detail);
    }
    /*
     * (non-PHPdoc) @see \repository\content_object\survey_page\QuestionDisplay::get_instruction()
     */
    public function get_instruction()
    {
        // TODO Auto-generated method stub
    }
}
?>