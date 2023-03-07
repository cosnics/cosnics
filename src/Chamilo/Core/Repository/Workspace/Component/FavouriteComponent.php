<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Table\WorkspaceTableRenderer;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;

/**
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteComponent extends TabComponent implements DelegateComponent
{

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function build()
    {
        $html = [];

        $html[] = $this->render_header();

        if (!$this->getWorkspaceService()->userHasWorkspaceFavourites($this->getUser()))
        {
            $html[] = '<div class="alert alert-info">';
            $html[] = $this->getTranslator()->trans('FavouritesInfo', [], Manager::CONTEXT);
            $html[] = '</div>';
        }

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
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = $this->getWorkspaceService()->countWorkspaceFavouritesByUser($this->getUser());
        $workspaceTableRenderer = $this->getWorkspaceTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $workspaceTableRenderer->getParameterNames(), $workspaceTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $workspaces = $this->getWorkspaceService()->getWorkspaceFavouritesByUser(
            $this->getUser(), $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
            $workspaceTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $workspaceTableRenderer->render($tableParameterValues, $workspaces);
    }
}