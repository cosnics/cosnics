<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Integration\Chamilo\Core\Repository\ContentObject\Survey;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Display\PageDisplay;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;

class Display extends PageDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, 
        AnswerServiceInterface $answerService)
    {
        $nodes = $complex_content_object_path_node->get_children();
        
        foreach ($nodes as $node)
        {
            if (! $node->is_root())
            {
                $question_display = QuestionDisplay :: factory($this->get_formvalidator(), $node, $answerService);
                $question_display->run();
            }
        }
    }

    function get_instruction()
    {
    }
}
?>