<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @package repository.lib
 *          A class to render a ContentObject.
 */
abstract class AbstractContentObjectRenditionImplementation
{

    private $context;

    private $content_object;

    public function __construct($context, ContentObject $content_object)
    {
        $this->context = $context;
        $this->content_object = $content_object;
    }

    /**
     *
     * @return the $context
     */
    public function get_context()
    {
        return $this->context;
    }

    /**
     *
     * @param $context the $context to set
     */
    public function set_context($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return the $content_object
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     *
     * @param $content_object the $content_object to set
     */
    public function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }

    abstract public function get_view();

    abstract public function get_format();
}
