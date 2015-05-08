<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component\Viewer\Form;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component\TabComponent;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;

class ViewerComponent extends TabComponent
{

    function build()
    {
        $html = array();
        
        $html[] = $this->render_header();
        
        if ($this->get_current_node()->is_root())
        {
            $form = new Form($this);
            $html[] = $form->toHtml();
        }
        else
        {
            $html[] = ContentObjectRenditionImplementation :: launch(
                $this->get_current_content_object(), 
                ContentObjectRendition :: FORMAT_HTML, 
                ContentObjectRendition :: VIEW_FULL, 
                $this);
        }
        
        $html[] = $this->get_hidden_fields();
        $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 1);
        $html[] =  ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath($namespace, true) . 'PageDisplay.js');
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    private function get_hidden_fields()
    {
        $html = array();
        $paramaters = $this->get_parent()->get_parameters();
        
        $ajaxNamespace = ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->get_parent());
        $ajaxContext = ClassnameUtilities :: getInstance()->getNamespaceParent($ajaxNamespace, 1) . '\Ajax';
        
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