<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib
 *          A class to export a ContentObject.
 */
abstract class ContentObjectExportImplementation extends AbstractContentObjectExportImplementation
{

    public static function launch(ContentObjectExportController $controller, ContentObject $content_object, $format, 
        $type)
    {
        return self::factory($controller, $content_object, $format, $type)->render();
    }

    public static function factory(ContentObjectExportController $controller, ContentObject $content_object, $format, 
        $type)
    {
        $class = $content_object->package() . '\Common\Export\\' .
             (string) StringUtilities::getInstance()->createString($format)->upperCamelize() . '\\' .
             (string) StringUtilities::getInstance()->createString($format)->upperCamelize() .
             (string) StringUtilities::getInstance()->createString($type)->upperCamelize() . 'ExportImplementation';
        
        if (! class_exists($class, true))
        {
            $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($format)->upperCamelize() .
                 '\Type\\' . StringUtilities::getInstance()->createString($format)->upperCamelize() .
                 (string) StringUtilities::getInstance()->createString($type)->upperCamelize() . 'ContentObjectExport';
            return new $class(new DummyContentObjectExportImplementation($controller, $content_object));
        }
        else
        {
            return new $class($controller, $content_object);
        }
    }

    public static function get_types(array $types = array())
    {
        $types[] = ContentObjectExport::FORMAT_CPO;
        return $types;
    }

    public static function get_types_for_object($content_object_namespace)
    {
        $class = $content_object_namespace . '\Common\ExportImplementation';
        
        if (! class_exists($class, true))
        {
            return self::get_types();
        }
        else
        {
            return $class::get_types();
        }
    }
}
