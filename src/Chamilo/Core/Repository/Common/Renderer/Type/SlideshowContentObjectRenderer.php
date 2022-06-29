<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\Slideshow\SlideshowRenderer;

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

        $filterData = FilterData::getInstance($workspace);

        $contentObjectCount = $contentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, ConditionFilterRenderer::factory($filterData, $workspace)
        );

        if ($contentObjectCount)
        {
            $contentObject = $contentObjectService->getContentObjectsByTypeForWorkspace(
                $filterData->getTypeDataClass(), $workspace, ConditionFilterRenderer::factory($filterData, $workspace),
                1, $slideshowIndex
            )->current();

            $contentObjectActions = $this->get_content_object_actions($contentObject);

            return $this->getSlideshowRenderer()->render(
                $this->get_repository_browser(), $contentObject, $contentObjectCount, $contentObjectActions,
                $this->get_parameters()
            );
        }

        return '';
    }

    protected function getSlideshowRenderer(): SlideshowRenderer
    {
        return $this->get_repository_browser()->getService(SlideshowRenderer::class);
    }
}
