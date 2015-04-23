<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Repository\ContentObject\Survey\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay;

class Display extends PageDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $nodes = $complex_content_object_path_node->get_children();
        
        foreach ($nodes as $node)
        {
            if (! $node->is_root())
            {
                $answer = $this->get_formvalidator()->get_parent()->get_answer(
                    $node->get_complex_content_object_item()->get_id());
                $question_display = QuestionDisplay :: factory($this->get_formvalidator(), $node, $answer);
                $question_display->run();
            }
        }
    }

    function get_instruction()
    {
    }
}
?>