<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlFullThumbnailRenditionImplementation extends HtmlRenditionImplementation
{

    /**
     *
     * @return string
     */
    public function render($parameters = null)
    {
        $this->initializeContainer();

        $object = $this->get_content_object();

        $fullViewHtml = ContentObjectRenditionImplementation::factory(
            $object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_INLINE, $this->get_context()
        )->render($parameters);

        $downloadUrl = Manager::get_document_downloader_url(
            $object->get_id(), $object->calculate_security_code()
        );

        return $this->getTwig()->render(
            'Chamilo\Core\Repository:full_thumbnail.html.twig', [
                "icon_path" => $object->getGlyph(IdentGlyph::SIZE_BIG)
                    ->render(),
                "title" => $object->get_title(),
                "download_url" => $downloadUrl,
                "full_view" => $fullViewHtml,
                "id" => $object->getId()
            ]
        );
    }
}
