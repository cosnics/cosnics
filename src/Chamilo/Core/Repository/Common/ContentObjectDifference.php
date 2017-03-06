<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: content_object_difference.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib
 */
/**
 * A class to display a ContentObject.
 */
abstract class ContentObjectDifference
{

    /**
     * The object.
     */
    private $object;

    /**
     * The learning object version.
     */
    private $version;

    /**
     * Constructor.
     * 
     * @param $object ContentObject The object to compare.
     * @param $version ContentObject The object to compare with.
     */
    public function __construct($version, $object)
    {
        $this->object = $object;
        $this->version = $version;
    }

    /**
     * Returns the object associated with this object.
     * 
     * @return ContentObject The object.
     */
    public function get_object()
    {
        return $this->object;
    }

    /**
     * Returns the object associated with this object.
     * 
     * @return ContentObject The object version.
     */
    public function get_version()
    {
        return $this->version;
    }

    public function render()
    {
        $object_string = $this->object->get_description();
        $object_string = str_replace('<p>', '', $object_string);
        $object_string = str_replace('</p>', "<br />\n", $object_string);
        $object_string = explode("\n", strip_tags($object_string));
        $version_string = $this->version->get_description();
        $version_string = str_replace('<p>', '', $version_string);
        $version_string = str_replace('</p>', "<br />\n", $version_string);
        $version_string = explode("\n", strip_tags($version_string));
        
        $difference = new \Diff($version_string, $object_string);
        $renderer = new \Diff_Renderer_Html_SideBySide();
        
        $renderedString = $difference->Render($renderer);

        $translator = Translation::getInstance();
        $renderedString = str_replace('Old Version', $translator->getTranslation('OldVersion'), $renderedString);
        $renderedString = str_replace('New Version', $translator->getTranslation('NewVersion'), $renderedString);

        return $renderedString;
    }

    /**
     * Creates an object that can display the given object in a standardized fashion.
     * 
     * @param $object ContentObject The object to display.
     * @return ContentObject
     */
    public static function factory(&$object, &$version)
    {
        $class = $object->package() . '\\Common\\' .
             ClassnameUtilities::getInstance()->getPackageNameFromNamespace($object->package()) . 'Difference';
        return new $class($object, $version);
    }
}
