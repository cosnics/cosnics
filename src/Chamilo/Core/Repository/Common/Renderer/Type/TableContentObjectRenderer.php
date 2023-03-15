<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Table\ContentObjectTableRenderer;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

class TableContentObjectRenderer extends ContentObjectRenderer
{
    protected ContentObjectService $contentObjectService;

    protected ContentObjectTableRenderer $contentObjectTableRenderer;

    protected RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler;

    protected Workspace $workspace;

    public function __construct(
        ContentObjectService $contentObjectService, ContentObjectTableRenderer $contentObjectTableRenderer,
        RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler, Workspace $workspace
    )
    {
        $this->contentObjectService = $contentObjectService;
        $this->contentObjectTableRenderer = $contentObjectTableRenderer;
        $this->requestTableParameterValuesCompiler = $requestTableParameterValuesCompiler;
        $this->workspace = $workspace;
    }

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

        $contentObjectTableRenderer = $this->getContentObjectTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $contentObjectTableRenderer->getParameterNames(), $contentObjectTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = $contentObjectService->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, ConditionFilterRenderer::factory(
            $filterData, $workspace
        ), $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $contentObjectTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $contentObjectTableRenderer->render($tableParameterValues, $contentObjects);
    }

    public function getContentObjectService(): ContentObjectService
    {
        return $this->contentObjectService;
    }

    public function getContentObjectTableRenderer(): ContentObjectTableRenderer
    {
        return $this->contentObjectTableRenderer;
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
