<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;

/**
 *
 * @package repository.lib.content_object.document
 */
/**
 * This class can be used to get the difference between documents
 */
class FileDifference extends ContentObjectDifference
{

    public function render()
    {
        $object = $this->get_object();
        $version = $this->get_version();

        $object_string = $object->get_filename() . ' (' . number_format($object->get_filesize() / 1024, 2, '.', '') .
             ' kb)';
        $object_string = explode("\n", strip_tags($object_string));

        $version_string = $version->get_filename() . ' (' . number_format($version->get_filesize() / 1024, 2, '.', '') .
             ' kb)';
        $version_string = explode("\n", strip_tags($version_string));

        $html = array();
        $html[] = parent::render();

        $difference = new \Diff($version_string, $object_string);
        $renderer = new \Diff_Renderer_Html_SideBySide();

        $html[] = $difference->Render($renderer);

        return implode(PHP_EOL, $html);
    }
}
