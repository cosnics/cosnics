<?php
namespace Chamilo\Core\Repository\Common\Rendition;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @package repository.lib
 *          A class to render a ContentObject.
 */
class DummyContentObjectRenditionImplementation extends AbstractContentObjectRenditionImplementation
{

    private $format;

    private $view;

    public function __construct($context, ContentObject $content_object, $format, $view)
    {
        parent :: __construct($context, $content_object);
        $this->format = $format;
        $this->view = $view;
    }

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_view()
    {
        return $this->view;
    }

    public function get_format()
    {
        return $this->format;
    }
}
