<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component\Viewer\Form;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

class ViewerComponent extends Manager
{

    function run()
    {
        $form = new Form($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->get_hidden_fields();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    private function get_hidden_fields()
    {
        $html = array();
        $paramaters = $this->get_parent()->get_parameters();
        
        $ajaxNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromObject(
            $this->get_parent());
        $ajaxContext = ClassnameUtilities :: getInstance()->getNamespaceParent($ajaxNamespace,1).'\Ajax';
        
        $paramaters[self :: PARAM_AJAX_CONTEXT] = $ajaxContext;
        foreach ($paramaters as $name => $value)
        {
            $html[] = '<input type="hidden" value="' . $value . '" name="param_' . $name . '">';
        }
        return implode(PHP_EOL, $html);
    }

    public function get_answer($complex_question_id)
    {
        return $this->get_parent()->get_answer($complex_question_id);
    }
}
?>