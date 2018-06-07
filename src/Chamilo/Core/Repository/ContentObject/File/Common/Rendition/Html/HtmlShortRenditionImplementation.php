<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    /**
     *
     * @return string
     */
    public function render($parameters)
    {
        $this->initializeContainer();

        $object = $this->get_content_object();

/*        $inlineHtml = ContentObjectRenditionImplementation::launch(
            $object,
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_INLINE,
            $this->get_context());
*/
        $fullViewHtml = ContentObjectRenditionImplementation::factory(
            $object,
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_INLINE,
            $this->get_context())->render($parameters);

        $downloadUrl = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code());

        return $this->getTwig()->render(
            'Chamilo\Core\Repository\ContentObject\File:full_thumbnail.html.twig', [
                "icon_path" => $object->get_icon_path(Theme::ICON_BIG),
                "title" => $object->get_title(),
                "download_url" => $downloadUrl,
                "full_view" => $fullViewHtml
            ]
        );
    }
}
