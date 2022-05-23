<?php
namespace Chamilo\Core\Repository\Workspace\Table\Share;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Table\Workspace
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ShareTableDataProvider extends DataClassTableDataProvider
{

    /**
     *
     * @var ContentObjectRelationService
     */
    protected $contentObjectRelationService;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    private $workspaceService;

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::count_data()
     */
    public function count_data($condition)
    {
        return $this->getContentObjectRelationService()->countAvailableWorkspacesForContentObjectsAndUser(
            $this->getWorkspaceService(), $this->getSelectedContentObjects(), $this->get_component()->get_user()
        );
    }

    /**
     *
     * @return ContentObjectRelationService
     */
    protected function getContentObjectRelationService()
    {
        if (!isset($this->contentObjectRelationService))
        {
            $this->contentObjectRelationService =
                new ContentObjectRelationService(new ContentObjectRelationRepository());
        }

        return $this->contentObjectRelationService;
    }

    /**
     *
     * @return ContentObject[]
     */
    protected function getSelectedContentObjects()
    {
        $contentObjectIdentifiers = $this->get_component()->get_parameter(Manager::PARAM_CONTENT_OBJECT_ID);
        if (!is_array($contentObjectIdentifiers))
        {
            $contentObjectIdentifiers = array($contentObjectIdentifiers);
        }

        $contentObjects = [];

        foreach ($contentObjectIdentifiers as $contentObjectIdentifier)
        {
            $contentObject = new ContentObject();
            $contentObject->setId($contentObjectIdentifier);

            $contentObjects[] = $contentObject;
        }

        return $contentObjects;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\WorkspaceService
     */
    protected function getWorkspaceService()
    {
        if (!isset($this->workspaceService))
        {
            $this->workspaceService = new WorkspaceService(new WorkspaceRepository());
        }

        return $this->workspaceService;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Workspace\Table\Workspace\WorkspaceTableDataProvider::retrieve_data()
     */
    public function retrieve_data($condition, $offset, $limit, $orderProperty = null)
    {
        return $this->getContentObjectRelationService()->getAvailableWorkspacesForContentObjectsAndUser(
            $this->getWorkspaceService(), $this->getSelectedContentObjects(), $this->get_component()->get_user(),
            $limit, $offset, $orderProperty
        );
    }
}