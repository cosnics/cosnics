<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.lib
 *          A class to render a ContentObject.
 */
abstract class ContentObjectRendition
{
    const FORMAT_XML = 'xml';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const VIEW_FULL = 'full';
    const VIEW_DESCRIPTION = 'description';
    /*
     * @deprecated
     */
    const VIEW_SHORT = 'full_thumbnail';
    const VIEW_FULL_THUMBNAIL = 'full_thumbnail';
    const VIEW_PREVIEW = 'preview';
    const VIEW_THUMBNAIL = 'thumbnail';
    const VIEW_MAIL = 'mail';
    const VIEW_INLINE = 'inline';
    const VIEW_FORM = 'form';

    private $rendition_implementation;

    public function __construct($rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    /**
     *
     * @return the $rendition_implementation
     */
    public function get_rendition_implementation()
    {
        return $this->rendition_implementation;
    }

    /**
     *
     * @param $rendition_implementation the $rendition_implementation to set
     */
    public function set_rendition_implementation($rendition_implementation)
    {
        $this->rendition_implementation = $rendition_implementation;
    }

    /**
     *
     * @return the $context
     */
    public function get_context()
    {
        return $this->rendition_implementation->get_context();
    }

    /**
     *
     * @param $context the $context to set
     */
    public function set_context($context)
    {
        $this->rendition_implementation->set_context($context);
    }

    /**
     *
     * @return the $content_object
     */
    public function get_content_object()
    {
        return $this->rendition_implementation->get_content_object();
    }

    /**
     *
     * @param $content_object the $content_object to set
     */
    public function set_content_object($content_object)
    {
        $this->rendition_implementation->set_content_object($content_object);
    }

    public static function launch($rendition_implementation)
    {
        return self::factory($rendition_implementation)->render();
    }

    public static function factory($rendition_implementation)
    {
        $class = __NAMESPACE__ . '\\' .
             (string) StringUtilities::getInstance()->createString($rendition_implementation->get_format())->upperCamelize() .
             '\Type\\' .
             (string) StringUtilities::getInstance()->createString($rendition_implementation->get_format())->upperCamelize() .
             (string) StringUtilities::getInstance()->createString($rendition_implementation->get_view())->upperCamelize() .
             'ContentObjectRendition';

        return new $class($rendition_implementation);
    }
}
