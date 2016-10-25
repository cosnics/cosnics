<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;

/**
 * $Id: assessment_open_question_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.open_question
 */
/**
 * This class can be used to get the difference between open question
 */
class AssessmentOpenQuestionDifference extends ContentObjectDifference
{

    public function render()
    {
        $object = $this->get_object();
        $version = $this->get_version();
        
        $object_string = $object->get_question_type();
        $version_string = $version->get_question_type();
        
        $html = array();
        $html[] = parent :: render();
        
        $difference = new \Diff($version_string, $object_string);
        $renderer = new \Diff_Renderer_Html_SideBySide();
        
        $html[] = $difference->Render($renderer);
        
        return implode(PHP_EOL, $html);
    }
}
