<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib
 *          A class to render a ContentObject.
 */
abstract class ContentObjectRendition
{
    public const FORMAT_HTML = 'html';
    public const FORMAT_JSON = 'json';

    public const VIEW_DESCRIPTION = 'description';
    public const VIEW_FORM = 'form';
    public const VIEW_FULL = 'full';
    public const VIEW_FULL_THUMBNAIL = 'full_thumbnail';
    public const VIEW_INLINE = 'inline';
    public const VIEW_MAIL = 'mail';
    public const VIEW_PREVIEW = 'preview';
    public const VIEW_SHORT = 'full_thumbnail';
    public const VIEW_THUMBNAIL = 'thumbnail';

    private $rendition_implementation;

    public function __construct($rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    public static function factory($rendition_implementation)
    {
        $class = __NAMESPACE__ . '\\' .
            (string) StringUtilities::getInstance()->createString($rendition_implementation->get_format())
                ->upperCamelize() . '\Type\\' .
            (string) StringUtilities::getInstance()->createString($rendition_implementation->get_format())
                ->upperCamelize() .
            (string) StringUtilities::getInstance()->createString($rendition_implementation->get_view())->upperCamelize(
            ) . 'ContentObjectRendition';

        return new $class($rendition_implementation);
    }

    /**
     * @return the $content_object
     */
    public function get_content_object()
    {
        return $this->rendition_implementation->get_content_object();
    }

    /**
     * @return the $rendition_implementation
     */
    public function get_rendition_implementation()
    {
        return $this->rendition_implementation;
    }

    public static function launch($rendition_implementation)
    {
        return self::factory($rendition_implementation)->render();
    }

    /**
     * @param $content_object the $content_object to set
     */
    public function set_content_object($content_object)
    {
        $this->rendition_implementation->set_content_object($content_object);
    }

    /**
     * @param $rendition_implementation the $rendition_implementation to set
     */
    public function set_rendition_implementation($rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }
}
