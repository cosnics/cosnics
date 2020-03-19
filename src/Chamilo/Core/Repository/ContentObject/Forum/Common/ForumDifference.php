<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Diff;
use Diff_Renderer_Html_SideBySide;

/**
 *
 * @package repository.lib.content_object.forum
 */

/**
 * This class can be used to get the difference between forums
 */
class ForumDifference extends ContentObjectDifference
{

    public function render()
    {
        $object = $this->get_object();
        $version = $this->get_version();

        $object_string = $object->get_locked();
        $object_string = explode(PHP_EOL, strip_tags($object_string));

        $version_string = $version->get_locked();
        $version_string = explode(PHP_EOL, strip_tags($version_string));

        $html = array();
        $html[] = parent::render();

        $difference = new Diff($version_string, $object_string);
        $renderer = new Diff_Renderer_Html_SideBySide();

        $html[] = $difference->Render($renderer);

        return implode(PHP_EOL, $html);
    }
}
