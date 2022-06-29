<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
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
        $slideshowIndex = $toolbrowser->getRequest()->query->get(SlideshowRenderer::PARAM_INDEX, 0);

        $publications = $this->get_publications($slideshowIndex, 1);
        $publication = $publications[0];

        if ($publication)
        {
            $contentObject = $this->get_content_object_from_publication($publication);
            $publicationActions = $this->get_publication_actions($publication, false)->get_items();

            $publicationCount = $this->get_publication_count();

            if ($contentObject)
            {
                return $this->getSlideshowRenderer()->render(
                    $this, $contentObject, $publicationCount, $publicationActions, $toolbrowser->get_parameters()
                );
            }
        }

        return '';
    }

    protected function getSlideshowRenderer(): SlideshowRenderer
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SlideshowRenderer::class);
    }
}
