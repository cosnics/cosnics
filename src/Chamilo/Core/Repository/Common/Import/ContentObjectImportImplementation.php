<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib A class to import a ContentObject.
 */
abstract class ContentObjectImportImplementation extends AbstractContentObjectImportImplementation
{

    public static function launch(ContentObjectImportController $controller, $content_object_type,
        ContentObjectImportParameters $content_object_import_parameters)
    {
        return self :: factory($controller, $content_object_type, $content_object_import_parameters)->import();
    }

    public static function post_process(ContentObjectImportController $controller, $content_object_type,
        ContentObjectImportParameters $content_object_import_parameters, $content_object)
    {
        return self :: factory($controller, $content_object_type, $content_object_import_parameters)->post_import(
            $content_object);
    }

    public static function factory(ContentObjectImportController $controller, $content_object_type,
        $content_object_import_parameters)
    {
        $class = $content_object_type :: package() . '\Common\Import\\' .
             (string) StringUtilities :: getInstance()->createString($controller :: FORMAT)->upperCamelize() .
             'ImportImplementation';

        if (! class_exists($class, true))
        {
            $class = __NAMESPACE__ . '\\' .
                 (string) StringUtilities :: getInstance()->createString($controller :: FORMAT)->upperCamelize() . '\\' .
                 (string) StringUtilities :: getInstance()->createString($controller :: FORMAT)->upperCamelize() .
                 'ContentObjectImport';
            return new $class(new DummyContentObjectImportImplementation($controller, $content_object_import_parameters));
        }
        else
        {
            return new $class($controller, $content_object_import_parameters);
        }
    }

    public static function get_types(array $types = array())
    {
        $types[] = ContentObjectImport :: FORMAT_CPO;
        return $types;
    }

    public static function get_types_for_object($content_object_namespace)
    {
        $class = $content_object_namespace . '\Common\ImportImplementation';

        if (! class_exists($class, true))
        {
            return self :: get_types();
        }
        else
        {
            return $class :: get_types();
        }
    }
}
