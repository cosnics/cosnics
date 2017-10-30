<?php
namespace Chamilo\Core\Repository\Ajax\Tables\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\Tables\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectTableDataProvider
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    private $dataClassRetrievesParameters;

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
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     * @param \Chamilo\Core\Repository\Workspace\Service\ContentObjectService $contentObjectService
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     */
    public function __construct(DataClassRetrievesParameters $dataClassRetrievesParameters,
        ContentObjectService $contentObjectService, WorkspaceInterface $workspaceImplementation)
    {
        $this->dataClassRetrievesParameters = $dataClassRetrievesParameters;
        $this->contentObjectService = $contentObjectService;
        $this->workspaceImplementation = $workspaceImplementation;
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
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    public function getDataClassRetrievesParameters()
    {
        return $this->dataClassRetrievesParameters;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters $dataClassRetrievesParameters
     */
    public function setDataClassRetrievesParameters(DataClassRetrievesParameters $dataClassRetrievesParameters)
    {
        $this->dataClassRetrievesParameters = $dataClassRetrievesParameters;
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

    /**
     *
     * @return string[][]
     */
    public function getTableRowData()
    {
        $dataClassRetrievesParameters = $this->getDataClassRetrievesParameters();

        $contentObjects = $this->getContentObjectService()->getContentObjectsByTypeForWorkspace(
            ContentObject::class,
            $this->getWorkspaceImplementation(),
            $dataClassRetrievesParameters->getCondition(),
            $dataClassRetrievesParameters->getCount(),
            $dataClassRetrievesParameters->getOffset(),
            $dataClassRetrievesParameters->getOrderBy());

        // TODO: This is where we would need a renderer based on some kind of column model
        $contentObjectData = array();

        $propertyPrefix = str_replace('\\', '_', ContentObject::class) . ':';

        while ($contentObject = $contentObjects->next_result())
        {
            $contentObjectData[] = array(
                $propertyPrefix . ContentObject::PROPERTY_TITLE => $contentObject->get_title(),
                $propertyPrefix . ContentObject::PROPERTY_DESCRIPTION => $contentObject->get_description(),
                $propertyPrefix . ContentObject::PROPERTY_MODIFICATION_DATE => $contentObject->get_modification_date());
        }

        return $contentObjectData;
    }

    /**
     *
     * @return integer
     */
    public function getTableRowCount()
    {
        return $this->getContentObjectService()->countContentObjectsByTypeForWorkspace(
            ContentObject::class,
            $this->getWorkspaceImplementation(),
            $this->getDataClassRetrievesParameters()->getCondition());
    }
}

