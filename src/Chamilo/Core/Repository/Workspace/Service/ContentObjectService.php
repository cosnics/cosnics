<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectService
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    private $contentObjectRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     */
    public function __construct(ContentObjectRepository $contentObjectRepository)
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    public function getContentObjectRepository()
    {
        return $this->contentObjectRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     */
    public function setContentObjectRepository($contentObjectRepository)
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     *
     * @param WorkspaceInterface $workspace
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getContentObjectsForWorkspace(WorkspaceInterface $workspace, $offset, $count, $orderProperty)
    {
        if ($workspace instanceof PersonalWorkspace)
        {
            return $this->getContentObjectRepository()->findAllInPersonalWorkspace(
                $workspace,
                $offset,
                $count,
                $orderProperty);
        }
        else
        {
            return $this->getContentObjectRepository()->findAllInWorkspace($workspace, $offset, $count, $orderProperty);
        }
    }
}