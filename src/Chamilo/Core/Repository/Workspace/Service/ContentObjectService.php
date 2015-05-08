<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;

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
    public function getContentObjectsForWorkspace(WorkspaceInterface $workspace,
        ConditionFilterRenderer $filterConditionRenderer, $offset, $count, $orderProperty)
    {
        if ($workspace instanceof PersonalWorkspace)
        {
            return $this->getContentObjectRepository()->findAllInPersonalWorkspace(
                $workspace,
                $filterConditionRenderer,
                $offset,
                $count,
                $orderProperty);
        }
        else
        {
            return $this->getContentObjectRepository()->findAllInWorkspace(
                $workspace,
                $filterConditionRenderer,
                $offset,
                $count,
                $orderProperty);
        }
    }

    public function countContentObjectsForWorkspace(WorkspaceInterface $workspace,
        ConditionFilterRenderer $filterConditionRenderer)
    {
        if ($workspace instanceof PersonalWorkspace)
        {
            return $this->getContentObjectRepository()->countAllInPersonalWorkspace(
                $workspace,
                $filterConditionRenderer);
        }
        else
        {
            return $this->getContentObjectRepository()->countAllInWorkspace($workspace, $filterConditionRenderer);
        }
    }
}