<?php
namespace Chamilo\Core\Repository\Workspace\Table\Share;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function countData(?Condition $condition = null): int
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getContentObjectRelationService()->getAvailableWorkspacesForContentObjectsAndUser(
            $this->getWorkspaceService(), $this->getSelectedContentObjects(), $this->get_component()->get_user(),
            $count, $offset, $orderBy
        );
    }
}