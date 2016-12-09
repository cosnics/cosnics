<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        $type_id = $object->get_question_type();
        
        switch ($type_id)
        {
            case 1 :
                $type = Translation::get('OpenQuestion');
                break;
            case 2 :
                $type = Translation::get('OpenQuestionWithDocument');
                break;
            case 3 :
                $type = Translation::get('DocumentQuestion');
                break;
            default :
                $type = Translation::get('OpenQuestion');
                break;
        }
        
        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $html[] = '<thead>';
        $html[] = '<tr><th>&nbsp;</th><th>&nbsp;</th></tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        $html[] = '<tr class="row_even">';
        $html[] = '<td>' . Translation::get('Type', null, Utilities::COMMON_LIBRARIES) . '</td>';
        $html[] = '<td>' . $type . '</td>';
        $html[] = '</tr>';
        $html[] = '<tr class="row_odd">';
        $html[] = '<td>' . Translation::get('Feedback') . '</td>';
        $html[] = '<td>' . $object->get_feedback() . '</td>';
        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }
}
