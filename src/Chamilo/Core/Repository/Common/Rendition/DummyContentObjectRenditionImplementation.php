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

    public function __construct(ContentObject $content_object, $format, $view)
    {
        parent::__construct($content_object);
        $this->format = $format;
        $this->view = $view;
    }

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_format()
    {
        return $this->format;
    }

    public function get_view()
    {
        return $this->view;
    }
}
