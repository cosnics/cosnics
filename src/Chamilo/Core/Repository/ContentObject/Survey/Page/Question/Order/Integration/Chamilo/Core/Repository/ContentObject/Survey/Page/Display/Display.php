<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;

class Display extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $this->complex_content_object_item = $complex_content_object_path_node->get_complex_content_object_item();
        $content_object = $complex_content_object_path_node->get_content_object();
        
        $rendition = ContentObjectRenditionImplementation :: factory(
            $content_object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_FORM, 
            $this);
        
        $rendition->render(
            $this->get_formvalidator(), 
            $complex_content_object_path_node->get_complex_content_object_item(), 
            $answer);
    }
}
?>