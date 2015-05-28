<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\ComplexPage;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Component\Viewer\Form;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Session\Request;

class ViewerComponent extends TabComponent
{
    const FORM_BACK = 'back';
    const FORM_NEXT = 'next';
    const FORM_SUBMIT = 'submit';

    private $current_step;

    /**
     * Runs this component and displays its output.
     */
    function build()
    {
        $this->current_step = Request :: get(self :: PARAM_STEP, 1);
        
        if ($this->is_form_submitted())
        {
            $action = $this->get_action();
            
            $this->save_answers();
            
            if ($action == self :: FORM_BACK)
            {
                $this->current_step = $this->current_step-1;
            }
            elseif ($action == self :: FORM_NEXT)
            {
                $this->current_step = $this->current_step+1;
            }
            elseif ($action == self :: FORM_SUBMIT)
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $this->get_finish_html();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
            
            $this->set_parameter(self :: PARAM_STEP, $this->current_step);
            $this->redirect(null, false, $this->get_parameters());
        }
        else
        {
            $form = new Form($this, $this->get_url(array(self :: PARAM_STEP => $this->current_step)));
            
            $html = array();
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->get_hidden_fields();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    private function get_progress_bar()
    {
        $html = array();
        $html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
        $html[] = '<div style="background-color: lightblue; height: 14px; width:' . 25 . 'px; text-align: center;">';
        $html[] = '</div>';
        $html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round(25) .
             '%</div></div>';
        return implode(PHP_EOL, $html);
    }

    private function is_form_submitted()
    {
        return ! is_null(Request :: post('_qf__' . Form :: FORM_NAME));
    }

    public function get_action()
    {
        $actions = array(self :: FORM_NEXT, self :: FORM_SUBMIT, self :: FORM_BACK);
        
        foreach ($actions as $action)
        {
            if (! is_null(Request :: post($action)))
            {
                return $action;
            }
        }
        
        return self :: FORM_NEXT;
    }

    private function get_hidden_fields()
    {
        $html = array();
        $paramaters = $this->get_parameters();
        
        $ajaxNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->get_parent());
        $ajaxContext = ClassnameUtilities :: getInstance()->getNamespaceParent($ajaxNamespace, 1) . '\Ajax';
        
        $paramaters[Manager :: PARAM_AJAX_CONTEXT] = $ajaxContext;
        $paramaters[Manager :: PARAM_STEP] = $this->get_current_step();
        foreach ($paramaters as $name => $value)
        {
            $html[] = '<input type="hidden" value="' . $value . '" name="param_' . $name . '">';
        }
        return implode(PHP_EOL, $html);
    }

    private function get_finish_html()
    {
        $html = array();
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="content_object" style="background-image: url(' .
             $this->get_root_content_object()->get_icon_path() . ');">';
        $html[] = '<div class="title">' . $this->get_root_content_object()->get_title() . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';
        $html[] = '<div class="description">';
        $html[] = $this->get_root_content_object()->get_finish_text();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        return implode(PHP_EOL, $html);
    }

    public function get_answer($complex_question_id)
    {
        return $this->get_parent()->get_answer($complex_question_id);
    }

    public function save_answers()
    {
        if ($this->get_current_complex_content_object_item() instanceof ComplexPage)
        {
            
            $nodes = $this->get_current_content_object()->get_complex_content_object_path()->get_nodes();
            
            foreach ($nodes as $node)
            {
                if (! $node->is_root())
                {
                    $complex_content_object_item = $node->get_complex_content_object_item();
                    $answer_ids = $complex_content_object_item->get_answer_ids();
                    $answers = array();
                    
                    foreach ($answer_ids as $answer_id)
                    {
                        $answer = Request :: post($answer_id);
                        if ($answer)
                        {
                            $answers[$answer_id] = Request :: post($answer_id);
                        }
                    }
                    
                    if (count($answers) > 0)
                    {
                        $this->get_parent()->save_answer($complex_content_object_item->get_id(), $answers);
                    }
                }
            }
        }
    }
}
?>