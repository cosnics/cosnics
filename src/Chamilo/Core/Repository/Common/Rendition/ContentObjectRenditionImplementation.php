<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib
 *          A class to render a ContentObject.
 */
abstract class ContentObjectRenditionImplementation extends AbstractContentObjectRenditionImplementation
{

    public static function factory(ContentObject $content_object, $format, $view)
    {
        $namespace = ClassnameUtilities::getInstance()->getNamespaceFromObject($content_object);
        $class = $content_object->package() . '\Common\Rendition\\' .
            (string) StringUtilities::getInstance()->createString($format)->upperCamelize() . '\\' .
            (string) StringUtilities::getInstance()->createString($format)->upperCamelize() .
            (string) StringUtilities::getInstance()->createString($view)->upperCamelize() . 'RenditionImplementation';

        if (!class_exists($class, true))
        {
            return new DummyContentObjectRenditionImplementation($content_object, $format, $view);
        }
        else
        {
            return new $class($content_object);
        }
    }

    public function get_format()
    {
        $class_name_parts = explode('_', ClassnameUtilities::getInstance()->getClassnameFromObject($this, true));

        return $class_name_parts[0];
    }

    public function get_view()
    {
        $class_name_parts = explode('_', ClassnameUtilities::getInstance()->getClassnameFromObject($this, true));

        return $class_name_parts[1];
    }

    public static function launch(ContentObject $content_object, $format, $view)
    {
        return self::factory($content_object, $format, $view)->render();
    }
}
