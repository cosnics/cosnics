<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Form\ViewerForm;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Interfaces\PageDisplayItem;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\ComplexPage;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
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

            $this->saveAnswers();

            if ($action == self :: FORM_BACK)
            {
                $this->current_step = $this->current_step - 1;
            }
            elseif ($action == self :: FORM_NEXT)
            {
                $this->current_step = $this->current_step + 1;
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
            // var_dump($_SESSION);

            $form = new ViewerForm($this, $this->get_url(array(self :: PARAM_STEP => $this->current_step)));

            $html = array();
            $html[] = $this->render_header();
            $html[] = $this->addHiddenFields($form);
            $html[] = $this->addJavascript();
            $html[] = $form->toHtml();
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
        return ! is_null(Request :: post('_qf__' . ViewerForm :: FORM_NAME));
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

    private function addHiddenFields($form)
    {
        $paramaters = $this->get_parameters();
        $answerServiceContext = $this->getApplicationConfiguration()->getAnswerService()->getServiceContext();

        $paramaters[AnswerServiceInterface :: PARAM_SERVICE_CONTEXT] = $answerServiceContext;
        $paramaters[self :: PARAM_STEP] = $this->get_current_step();
        $paramaters[\Chamilo\Core\Repository\ContentObject\Survey\Ajax\Manager :: PARAM_CONTENT_OBJECT_ID] = $this->get_root_content_object_id();

        foreach ($paramaters as $name => $value)
        {
            $form->addHiddenField($name, $value);
        }
    }

    private function addJavascript()
    {
        return ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\Survey\Ajax', true) .
                 'ProcessVisibility.js');
    }

    private function get_finish_html()
    {
        $html = array();

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $this->get_root_content_object()->get_icon_image() . ' ' .
             $this->get_root_content_object()->get_title() . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $this->get_root_content_object()->get_finish_text();
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    private function saveAnswers()
    {
        if ($this->get_current_complex_content_object_item() instanceof PageDisplayItem)
        {
            $this->saveAnswer($this->get_current_node());
        }
        elseif ($this->get_current_complex_content_object_item() instanceof ComplexPage)
        {
            $nodes = $this->get_current_complex_content_object_path_node()->get_descendants();

            foreach ($nodes as $node)
            {
                if ($node->get_complex_content_object_item() instanceof PageDisplayItem)
                {
                    $this->saveAnswer($node);
                }
            }
        }
    }

    private function saveAnswer(ComplexContentObjectPathNode $node)
    {
        $answerService = $this->getApplicationConfiguration()->getAnswerService();
        $complexContentObjectItem = $node->get_complex_content_object_item();
        $answerIds = $complexContentObjectItem->getAnswerIds($answerService->getPrefix());

        $answers = $answerService->getAnswer($node->get_id());

        foreach ($answerIds as $answerId)
        {
            $answer = Request :: post($answerId);
            if ($answer)
            {
                $answers[$answerId] = $answer;
            }
            else
            {
                unset($answers[$answerId]);
            }
        }

        if ($answers)
        {
            $answerService->saveAnswer($node->get_id(), $answers);
        }
    }
}
?>