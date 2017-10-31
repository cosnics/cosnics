<?php
namespace Chamilo\Core\Repository\Ajax\DataTable\Type\ContentObject;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\DataTable\Service\DataTableProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\DataTable\Type\ContentObject
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class ContentObjectDataTableProvider extends DataTableProvider
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    private $contentObjectService;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspaceImplementation;

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Service\DataTableProvider::__construct()
     */
    public function __construct(\Chamilo\Libraries\Format\DataTable\DataTableCellRenderer $dataTableCellRenderer,
        \Chamilo\Libraries\Format\DataTable\DataTableColumnModel $dataTableColumnModel,
        ContentObjectService $contentObjectService)
    {
        parent::__construct($dataTableCellRenderer, $dataTableColumnModel);

        $this->contentObjectService = $contentObjectService;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\ContentObjectService
     */
    public function getContentObjectService()
    {
        return $this->contentObjectService;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Service\ContentObjectService $contentObjectService
     */
    public function setContentObjectService(ContentObjectService $contentObjectService)
    {
        $this->contentObjectService = $contentObjectService;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Service\DataTableProvider::getDataTableDataClasses()
     */
    public function getDataTableDataClasses(DataClassRetrievesParameters $dataClassRetrievesParameters,
        WorkspaceInterface $workspaceInstance)
    {
        return $this->getContentObjectService()->getContentObjectsByTypeForWorkspace(
            ContentObject::class,
            $workspaceInstance,
            $dataClassRetrievesParameters->getCondition(),
            $dataClassRetrievesParameters->getCount(),
            $dataClassRetrievesParameters->getOffset(),
            $dataClassRetrievesParameters->getOrderBy())->as_array();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceInstance
     * @return string[][]
     */
    public function getDataTableRowData(DataClassRetrievesParameters $dataClassRetrievesParameters,
        WorkspaceInterface $workspaceInstance)
    {
        $dataTableRowData = array();

        foreach ($this->getDataTableDataClasses($dataClassRetrievesParameters, $workspaceInstance) as $dataClass)
        {
            $dataTableRowData[] = $this->handleDataClass($dataClass);
        }

        return $dataTableRowData;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceInstance
     * @return integer
     */
    public function getDataTableRowCount(DataClassRetrievesParameters $dataClassRetrievesParameters,
        WorkspaceInterface $workspaceInstance)
    {
        return $this->getContentObjectService()->countContentObjectsByTypeForWorkspace(
            ContentObject::class,
            $workspaceInstance,
            $dataClassRetrievesParameters->getCondition());
    }
}

