<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib A class to import a ContentObject.
 */
abstract class ContentObjectImport
{
    const FORMAT_CPO = 'cpo';
    const FORMAT_ICAL = 'ical';
    const FORMAT_QTI = 'qti';
    const FORMAT_DOCUMENT = 'document';
    const FORMAT_ZIP = 'zip';
    const FORMAT_RAR = 'rar';
    const FORMAT_YOUTUBE = 'youtube';
    const FORMAT_VIMEO = 'vimeo';
    const FORMAT_HOTPOTATOES = 'hotpotatoes';
    // const FORMAT_SCORM = 'scorm';
    const FORMAT_FILE = 'file';
    const FORMAT_WEBPAGE = 'webpage';
    const TYPE_DEFAULT = 'default';

    private $import_implementation;

    public function __construct($import_implementation)
    {
        $this->import_implementation = $import_implementation;
    }

    /**
     *
     * @return AbstractContentObjectImportImplementation
     */
    public function get_import_implementation()
    {
        return $this->import_implementation;
    }

    /**
     *
     * @param $import_implementation the $import_implementation to set
     */
    public function set_import_implementation($import_implementation)
    {
        $this->import_implementation = $import_implementation;
    }

    /**
     *
     * @return ContentObjectImportController
     */
    public function get_context()
    {
        return $this->import_implementation->get_context();
    }

    /**
     *
     * @param $context the $context to set
     */
    public function set_context($context)
    {
        $this->import_implementation->set_context($context);
    }

    /**
     *
     * @return ContentObject
     */
    public function get_content_object()
    {
        return $this->import_implementation->get_content_object();
    }

    /**
     *
     * @param $content_object the $content_object to set
     */
    public function set_content_object($content_object)
    {
        $this->import_implementation->set_content_object($content_object);
    }

    public static function launch($import_implementation)
    {
        return self :: factory($import_implementation)->import();
    }

    public static function post_process($import_implementation, $content_object)
    {
        return self :: factory($import_implementation)->post_import($content_object);
    }

    public static function factory($import_implementation)
    {
        $class_name = ClassnameUtilities :: getInstance()->getClassnameFromObject($import_implementation, true);
        $class_name_parts = explode('_', $class_name);
        $class = __NAMESPACE__ . '\\' .
             (string) StringUtilities :: getInstance()->createString($class_name_parts[0])->upperCamelize() . '\\' .
             (string) StringUtilities :: getInstance()->createString($class_name_parts[0])->upperCamelize() .
             'ContentObjectImport';
        return new $class($import_implementation);
    }
}
