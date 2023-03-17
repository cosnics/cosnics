<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Table\ContentObjectTableRenderer;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceContentObjectService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

class TableContentObjectRenderer extends ContentObjectRenderer
{
    protected ContentObjectTableRenderer $contentObjectTableRenderer;

    protected RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler;

    protected Workspace $workspace;

    protected WorkspaceContentObjectService $workspaceContentObjectService;

    public function __construct(
        WorkspaceContentObjectService $workspaceContentObjectService,
        ContentObjectTableRenderer $contentObjectTableRenderer,
        RequestTableParameterValuesCompiler $requestTableParameterValuesCompiler, Workspace $workspace
    )
    {
        $this->workspaceContentObjectService = $workspaceContentObjectService;
        $this->contentObjectTableRenderer = $contentObjectTableRenderer;
        $this->requestTableParameterValuesCompiler = $requestTableParameterValuesCompiler;
        $this->workspace = $workspace;
    }

    public function render(): string
    {
        $workspaceContentObjectService = $this->getWorkspaceContentObjectService();
        $workspace = $this->getWorkspace();

        $filterData = FilterData::getInstance($workspace);

        $totalNumberOfItems = $workspaceContentObjectService->countContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, ConditionFilterRenderer::factory(
            $filterData, $workspace
        )
        );

        $contentObjectTableRenderer = $this->getContentObjectTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $contentObjectTableRenderer->getParameterNames(), $contentObjectTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $contentObjects = $workspaceContentObjectService->getContentObjectsByTypeForWorkspace(
            $filterData->getTypeDataClass(), $workspace, ConditionFilterRenderer::factory(
            $filterData, $workspace
        ), $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $contentObjectTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $contentObjectTableRenderer->render($tableParameterValues, $contentObjects);
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

    public function getWorkspaceContentObjectService(): WorkspaceContentObjectService
    {
        return $this->workspaceContentObjectService;
    }
}
