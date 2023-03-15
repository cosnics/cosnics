<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;

class HtmlFullThumbnailContentObjectRendition extends HtmlContentObjectRendition
{
    use DependencyInjectionContainerTrait;

    /*
     * @return string
     */
    public function render($parameters = null)
    {
        $this->initializeContainer();

        $object = $this->get_content_object();

        $fullViewHtml = ContentObjectRenditionImplementation::factory(
            $object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_INLINE
        )->render($parameters);

        return $this->getTwig()->render(
            'Chamilo\Core\Repository:full_thumbnail.html.twig', [
                'icon' => $object->getGlyph(IdentGlyph::SIZE_BIG)->render(),
                'title' => $object->get_title(),
                'full_view' => $fullViewHtml,
                'id' => $object->getId()
            ]
        );
    }
}
