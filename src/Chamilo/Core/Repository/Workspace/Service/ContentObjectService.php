<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;

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
     * @param string $contentObjectClassName
     * @param WorkspaceInterface $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return int
     */
    public function countContentObjectsByTypeForWorkspace(
        $contentObjectClassName, WorkspaceInterface $workspace, ConditionFilterRenderer $filterConditionRenderer
    )
    {
        $contentObjectClassName = empty($contentObjectClassName) ? ContentObject::class : $contentObjectClassName;

        if ($workspace instanceof PersonalWorkspace)
        {
            return $this->getContentObjectRepository()->countAllInPersonalWorkspace(
                $contentObjectClassName, $workspace, $filterConditionRenderer
            );
        }
        else
        {
            return $this->getContentObjectRepository()->countAllInWorkspace(
                $contentObjectClassName, $workspace, $filterConditionRenderer
            );
        }
    }

    /**
     *
     * @param WorkspaceInterface $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     *
     * @return int
     * @deprecated Use ContentObjectService::countContentObjectsByTypeForWorkspace
     *
     */
    public function countContentObjectsForWorkspace(
        WorkspaceInterface $workspace, ConditionFilterRenderer $filterConditionRenderer
    )
    {
        return $this->countContentObjectsByTypeForWorkspace(
            ContentObject::class, $workspace, $filterConditionRenderer
        );
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
     * Returns a list of content objects in a given workspace by a specific type
     *
     * @param string $contentObjectClassName
     * @param WorkspaceInterface $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     */
    public function getContentObjectsByTypeForWorkspace(
        $contentObjectClassName, WorkspaceInterface $workspace, ConditionFilterRenderer $filterConditionRenderer,
        $offset = null, $count = null, $orderProperty = []
    )
    {
        $contentObjectClassName = empty($contentObjectClassName) ? ContentObject::class : $contentObjectClassName;

        if ($workspace instanceof PersonalWorkspace)
        {
            return $this->getContentObjectRepository()->findAllInPersonalWorkspace(
                $contentObjectClassName, $workspace, $filterConditionRenderer, $offset, $count, $orderProperty
            );
        }
        else
        {
            return $this->getContentObjectRepository()->findAllInWorkspace(
                $contentObjectClassName, $workspace, $filterConditionRenderer, $offset, $count, $orderProperty
            );
        }
    }

    /**
     *
     * @param WorkspaceInterface $workspace
     * @param ConditionFilterRenderer $filterConditionRenderer
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     *
     * @deprecated Use ContentObjectService::getContentObjectsByTypeForWorkspace
     */
    public function getContentObjectsForWorkspace(
        WorkspaceInterface $workspace, ConditionFilterRenderer $filterConditionRenderer, $offset, $count, $orderProperty
    )
    {
        return $this->getContentObjectsByTypeForWorkspace(
            ContentObject::class, $workspace, $filterConditionRenderer, $offset, $count, $orderProperty
        );
    }
}