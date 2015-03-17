<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\Format\Form\FormValidator;

class Form extends FormValidator
{
    const FORM_NAME = 'survey_page_viewer_form';

    private $parent;

    function __construct($parent)
    {
        parent :: __construct(self :: FORM_NAME, 'post');
        $this->parent = $parent;
        $this->buildForm();
    }

    function buildForm()
    {
        $nodes = $this->parent->get_root_content_object()->get_complex_content_object_path()->get_nodes();
        
        foreach ($nodes as $node)
        {
            if (! $node->is_root())
            {
                
                $answer = $this->parent->get_answer($node->get_complex_content_object_item()->get_id());
                $question_display = QuestionDisplay :: factory($this, $node, $answer);
                $question_display->run();
            }
        }
    }
}
?>