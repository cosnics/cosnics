<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;

/**
 *
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class can be used to get the difference between physical_locations
 */
class PhysicalLocationDifference extends ContentObjectDifference
{

    public function render()
    {
        $object = $this->get_object();
        $version = $this->get_version();

        $object_string = $object->get_location();
        $object_string = explode("\n", strip_tags($object_string));

        $version_string = $version->get_location();
        $version_string = explode("\n", strip_tags($version_string));

        $html = array();
        $html[] = parent::render();

        $difference = new \Diff($version_string, $object_string);
        $renderer = new \Diff_Renderer_Html_SideBySide();

        $html[] = $difference->Render($renderer);

        return implode(PHP_EOL, $html);
    }
}
