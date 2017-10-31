<?php
namespace Chamilo\Core\Repository\Ajax\DataTable\Type\ContentObject;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\DataTable\Service\DataTableProvider;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

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
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getWorkspaceImplementation()
    {
        return $this->workspaceImplementation;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     */
    public function setWorkspaceImplementation(WorkspaceInterface $workspaceImplementation)
    {
        $this->workspaceImplementation = $workspaceImplementation;
    }

    public function validateDataTableProviderConfiguration()
    {
        if (! $this->getContentObjectService() instanceof ContentObjectService)
        {
            throw new \InvalidArgumentException(
                'Please configure a ContentObjectService for the ContentObjectDataTableProvider before attempting to use it');
        }

        if (! $this->getWorkspaceImplementation() instanceof WorkspaceInterface)
        {
            throw new \InvalidArgumentException(
                'Please configure a Workspace-instance for the ContentObjectDataTableProvider before attempting to use it');
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\DataTable\Service\DataTableProvider::getDataTableDataClasses()
     */
    public function getDataTableDataClasses(DataClassRetrievesParameters $dataClassRetrievesParameters)
    {
        $this->validateDataTableProviderConfiguration();

        return $this->getContentObjectService()->getContentObjectsByTypeForWorkspace(
            ContentObject::class,
            $this->getWorkspaceImplementation(),
            $dataClassRetrievesParameters->getCondition(),
            $dataClassRetrievesParameters->getCount(),
            $dataClassRetrievesParameters->getOffset(),
            $dataClassRetrievesParameters->getOrderBy())->as_array();
    }

    /**
     *
     * @return integer
     */
    public function getDataTableRowCount(DataClassRetrievesParameters $dataClassRetrievesParameters)
    {
        $this->validateDataTableProviderConfiguration();

        return $this->getContentObjectService()->countContentObjectsByTypeForWorkspace(
            ContentObject::class,
            $this->getWorkspaceImplementation(),
            $dataClassRetrievesParameters->getCondition());
    }
}

