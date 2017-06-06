<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;

class ComplexContentObjectEmbedder extends Embedder
{

    /**
     *
     * @see \core\repository\content_object\learning_path\display\Embedder::render()
     */
    public function render()
    {
        $link = $this->get_application()->get_url($this->get_parameters());
        
        $html = array();
        
        $html[] = '<iframe frameborder="0" class="link_iframe" src="' . $link . '" width="100%" height="700px">';
        $html[] = '<p>Your browser does not support iframes.</p></iframe>';
        
        $html[] = '<script type="text/javascript">';
        $html[] = 'function resizeIframe() {';
        $html[] = '     $(".link_iframe")[0].height = $(".link_iframe")[0].contentWindow.document.body.offsetHeight + 20;';
        $html[] = '}';
        $html[] = '$(".link_iframe").on("load", function() {';
        $html[] = '     resizeIframe();';
        $html[] = '     setTimeout(resizeIframe, 750);';
        $html[] = '});';
        
        $html[] = '</script>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function get_parameters()
    {
        $parameters = array();
        $parameters[Manager::PARAM_ACTION] = null;
        $parameters[self::PARAM_EMBEDDED_CONTENT_OBJECT_ID] = $this->treeNode->getContentObject()->getId();
        return $parameters;
    }
}