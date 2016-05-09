<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Format\Slideshow\SlideshowRenderer;

/**
 *
 * @package Chamilo\Application\Weblcms\Renderer\PublicationList\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SlideshowContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{

    /**
     *
     * @see \Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer::as_html()
     */
    public function as_html()
    {
        $toolbrowser = $this->get_tool_browser();
        $slideshowIndex = $toolbrowser->getRequest()->query->get(SlideshowRenderer :: PARAM_INDEX, 0);
        $slideshowAutoPlay = $toolbrowser->getRequest()->query->get(SlideshowRenderer :: PARAM_AUTOPLAY, 0);

        $publications = $this->get_publications($slideshowIndex, 1);
        $publication = $publications[0];

        $contentObject = $publicationActions = null;
        if ($publication)
        {
            $contentObject = $this->get_content_object_from_publication($publication);
            $publicationActions = $this->get_publication_actions($publication, false)->get_items();
        }

        $publicationCount = $this->get_publication_count();

        $contentObjectRenditionImplementation = null;
        if ($contentObject)
        {
            $contentObjectRenditionImplementation = ContentObjectRenditionImplementation :: factory(
                $contentObject,
                ContentObjectRendition :: FORMAT_HTML,
                ContentObjectRendition :: VIEW_PREVIEW,
                $this);
        }

        $slideshowRender = new SlideshowRenderer(
            $contentObject,
            $publicationCount,
            $contentObjectRenditionImplementation,
            $publicationActions,
            $toolbrowser->get_parameters(),
            $slideshowIndex,
            $slideshowAutoPlay);

        return $slideshowRender->render();
    }
}
