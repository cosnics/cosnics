<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_description
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFullRenditionImplementation extends HtmlRenditionImplementation
{

    function render()
    {
        $html[] = ContentObjectRendition :: launch($this);
        $html[] = '<h4>' . Translation :: get('InfoPreview') . '</h4>';
        $html[] = '<div style="border: 1px solid whitesmoke; padding: 10px; margin-bottom: 10px;">';
        $html[] = $this->get_question_preview();
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_question_preview($nr = null)
    {
        $content_object = $this->get_content_object();
        
        $html = array();
        
        $html[] = '<div class="survey">';
        $html[] = '<div class="information">';
        $html[] = $content_object->get_description();
        
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html);
    }
}
?>