<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;

/**
 * $Id: forum_topic_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.forum_topic
 */
/**
 * This class can be used to get the difference between forum topics
 */
class ForumTopicDifference extends ContentObjectDifference
{

    public function render()
    {
        $object = $this->get_object();
        $version = $this->get_version();
        
        $object_string = $object->get_locked();
        $object_string = explode("\n", strip_tags($object_string));
        
        $version_string = $version->get_locked();
        $version_string = explode("\n", strip_tags($version_string));
        
        $html = array();
        $html[] = parent::render();
        
        $difference = new \Diff($version_string, $object_string);
        $renderer = new \Diff_Renderer_Html_SideBySide();
        
        $html[] = $difference->Render($renderer);
        
        return implode(PHP_EOL, $html);
    }
}
