<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Table\ContentObjectGalleryTableRenderer;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

/**
 * Renderer to display a sortable table with object publications.
 */
class GalleryTableContentObjectRenderer extends ContentObjectRenderer
{
    protected ContentObjectGalleryTableRenderer $contentObjectGalleryTableRenderer;

    protected ContentObjectService $contentObjectService;

    protected RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler;

    protected Workspace $workspace;

    public function __construct(
        ContentObjectService $contentObjectService,
        ContentObjectGalleryTableRenderer $contentObjectGalleryTableRenderer,
        RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler, Workspace $workspace
    )
    {
        $this->contentObjectService = $contentObjectService;
        $this->contentObjectGalleryTableRenderer = $contentObjectGalleryTableRenderer;
        $this->requestTableParameterValuesCompiler = $requestTableParameterValuesCompiler;
        $this->workspace = $workspace;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function render(): string
    {
        $contentObjectService = $this->getContentObjectService();
        $workspace = $this->getWorkspace();

        $filterData = FilterData::getInstance($workspace);

        $totalNumberOfItems = $contentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, ConditionFilterRenderer::factory(
            $filterData, $workspace
        )
        );

        $contentObjectGalleryTableRenderer = $this->getContentObjectGalleryTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $contentObjectGalleryTableRenderer->getParameterNames(),
            $contentObjectGalleryTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $contentObjects = $contentObjectService->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, ConditionFilterRenderer::factory(
            $filterData, $workspace
        ), $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $contentObjectGalleryTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $contentObjectGalleryTableRenderer->render($tableParameterValues, $contentObjects);
    }

    public function getContentObjectGalleryTableRenderer(): ContentObjectGalleryTableRenderer
    {
        return $this->contentObjectGalleryTableRenderer;
    }

    public function getContentObjectService(): ContentObjectService
    {
        return $this->contentObjectService;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->requestTableParameterValuesCompiler;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }
}
