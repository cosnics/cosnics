<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib
 *          A class to export a ContentObject.
 */
abstract class ContentObjectExport
{
    use DependencyInjectionContainerTrait;

    public const FORMAT_CPO = 'Cpo';
    public const FORMAT_HTML = 'Html';
    public const FORMAT_ICAL = 'Ical';
    public const FORMAT_ZIP = 'Zip';

    public const TYPE_DEFAULT = 'Default';

    private $export_implementation;

    public function __construct($export_implementation)
    {
        $this->export_implementation = $export_implementation;
    }

    public static function factory($export_implementation)
    {
        $class_name = ClassnameUtilities::getInstance()->getClassnameFromObject($export_implementation, true);
        $class_name_parts = explode('_', $class_name);

        $class = __NAMESPACE__ . '\\' .
            (string) StringUtilities::getInstance()->createString($class_name_parts[0])->upperCamelize() . '\Type\\' .
            (string) StringUtilities::getInstance()->createString($class_name_parts[0])->upperCamelize() .
            (string) StringUtilities::getInstance()->createString($class_name_parts[1])->upperCamelize() .
            'ContentObjectExport';

        return new $class($export_implementation);
    }

    protected function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->getService(ContentObjectRelationService::class);
    }

    /**
     * @return ContentObject
     */
    public function get_content_object()
    {
        return $this->export_implementation->get_content_object();
    }

    /**
     * @return ContentObjectExportController
     */
    public function get_context()
    {
        return $this->export_implementation->get_context();
    }

    /**
     * @return ContentObjectExport
     */
    public function get_export_implementation()
    {
        return $this->export_implementation;
    }

    public static function get_types()
    {
        return [self::FORMAT_CPO, self::FORMAT_ICAL, self::FORMAT_ZIP, self::FORMAT_HTML];
    }

    public static function launch($export_implementation)
    {
        return self::factory($export_implementation)->render();
    }

    /**
     * @param $content_object the $content_object to set
     */
    public function set_content_object($content_object)
    {
        $this->export_implementation->set_content_object($content_object);
    }

    /**
     * @param $context the $context to set
     */
    public function set_context($context)
    {
        $this->export_implementation->set_context($context);
    }

    /**
     * @param $export_implementation the $export_implementation to set
     */
    public function set_export_implementation($export_implementation)
    {
        $this->export_implementation = $export_implementation;
    }
}
