<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Component;

use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelationTableRenderer;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager
{

    public function run()
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getEntityRelationCondition(): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($this->getCurrentWorkspace()->getId())
        );
    }

    public function getEntityRelationTableRenderer(): EntityRelationTableRenderer
    {
        return $this->getService(EntityRelationTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            $this->getEntityRelationService()->countEntityRelations($this->getEntityRelationCondition());
        $entityRelationTableRenderer = $this->getEntityRelationTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $entityRelationTableRenderer->getParameterNames(),
            $entityRelationTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $workspaceEntityRelations = $this->getEntityRelationService()->getEntityRelations(
            $this->getEntityRelationCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset()
        );

        return $entityRelationTableRenderer->render($tableParameterValues, $workspaceEntityRelations);
    }
}