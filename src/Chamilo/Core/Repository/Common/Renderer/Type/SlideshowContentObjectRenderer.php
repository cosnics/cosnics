<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Slideshow\SlideshowRenderer;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\Common\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SlideshowContentObjectRenderer extends ContentObjectRenderer
{

    /**
     *
     * @see \Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer::as_html()
     */
    public function as_html()
    {
        $application = $this->get_repository_browser();
        $workspace = $application->getWorkspace();
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());
        
        $slideshowIndex = $application->getRequest()->query->get(SlideshowRenderer::PARAM_INDEX, 0);
        $slideshowAutoPlay = $application->getRequest()->query->get(SlideshowRenderer::PARAM_AUTOPLAY, 0);
        
        $filterData = FilterData::getInstance($workspace);

        $contentObjectCount = $contentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(),
            $workspace,
            ConditionFilterRenderer::factory($filterData, $workspace));

        $contentObject = $contentObjectRenditionImplementation = $contentObjectActions = null;

        if($contentObjectCount)
        {
            $contentObject = $contentObjectService->getContentObjectsByTypeForWorkspace(
                $filterData->getTypeDataClass(),
                $workspace,
                ConditionFilterRenderer::factory($filterData, $workspace),
                1,
                $slideshowIndex,
                array()
            )->next_result();

            $contentObjectRenditionImplementation = ContentObjectRenditionImplementation::factory(
                $contentObject,
                ContentObjectRendition::FORMAT_HTML,
                ContentObjectRendition::VIEW_PREVIEW,
                $this->get_repository_browser()
            );

            $contentObjectActions = $this->get_content_object_actions($contentObject);
        }
        
        $slideshowRender = new SlideshowRenderer(
            $contentObject, 
            $contentObjectCount, 
            $contentObjectRenditionImplementation, 
            $contentObjectActions,
            $this->get_parameters(), 
            $slideshowIndex, 
            $slideshowAutoPlay);
        
        return $slideshowRender->render();
    }
}
