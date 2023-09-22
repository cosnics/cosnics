<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Core\Repository\Workspace\Table\WorkspaceTableRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

/**
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PersonalBrowserComponent extends TabComponent
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function build()
    {
        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->renderTable();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getWorkspaceTableRenderer(): WorkspaceTableRenderer
    {
        return $this->getService(WorkspaceTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getWorkspaceService()->countWorkspacesByCreator($this->getUser());
        $workspaceTableRenderer = $this->getWorkspaceTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $workspaceTableRenderer->getParameterNames(), $workspaceTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $workspaces = $this->getWorkspaceService()->getWorkspacesByCreator(
            $this->getUser(), $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $workspaceTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $workspaceTableRenderer->render($tableParameterValues, $workspaces);
    }
}