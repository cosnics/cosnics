<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;

class HtmlShortContentObjectRendition extends HtmlContentObjectRendition
{
    use DependencyInjectionContainerTrait;

    public function render($parameters = null)
    {
        $object = $this->get_content_object();

        $this->initializeContainer();

        $this->initializeContainer();

        $fullViewHtml = ContentObjectRenditionImplementation::factory(
            $object,
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_INLINE,
            $this->get_context())->render($parameters);

        return $this->getTwig()->render(
            'Chamilo\Core\Repository:full_thumbnail.html.twig', [
                "icon_path" => $object->get_icon_path(Theme::ICON_BIG),
                "title" => $object->get_title(),
                "full_view_html" => $fullViewHtml
            ]
        );
    }
}
