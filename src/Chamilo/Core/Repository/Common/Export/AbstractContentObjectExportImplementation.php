<?php
namespace Chamilo\Core\Repository\Common\Export;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @package repository.lib
 *          A class to export a ContentObject.
 */
abstract class AbstractContentObjectExportImplementation
{

    private $context;

    private $content_object;

    /**
     *
     * @param ContentObjectExportController $context
     * @param ContentObject $content_object
     */
    public function __construct(ContentObjectExportController $context, ContentObject $content_object)
    {
        $this->context = $context;
        $this->content_object = $content_object;
    }

    /**
     *
     * @return ContentObjectExportController
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
     * @return \core\repository\ContentObject
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
}
