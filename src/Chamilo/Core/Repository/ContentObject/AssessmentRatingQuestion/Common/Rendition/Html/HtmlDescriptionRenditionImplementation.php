<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $question = $this->get_content_object();
        $min = $question->get_low();
        $max = $question->get_high();
        
        $html = array();
        $html[] = '<div class="question">';
        $html[] = '<div class="answer">';
        $html[] = '<select class="rating_slider">';
        $html[] = '</option>';
        for ($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
            $html[] = '<option value="' . $i . '" > ' . $i . '';
            $html[] = '</option>';
        }
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion', 
                true) . 'AssessmentRatingQuestion.js');
        
        return implode(PHP_EOL, $html);
    }
}
