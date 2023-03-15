<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package repository.lib
 *          A class to render a ContentObject.
 */
abstract class AbstractContentObjectRenditionImplementation
{

    private $content_object;

    public function __construct(ContentObject $content_object)
    {
        $this->content_object = $content_object;
    }

    /**
     * @return
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    abstract public function get_format();

    abstract public function get_view();

    /**
     * @param $content_object
     */
    public function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }
}
