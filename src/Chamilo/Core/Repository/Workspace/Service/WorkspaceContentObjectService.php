<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceContentObjectService
{

    private ContentObjectRepository $contentObjectRepository;

    public function __construct(ContentObjectRepository $contentObjectRepository)
    {
        $this->contentObjectRepository = $contentObjectRepository;
    }

    public function countContentObjectsByTypeForWorkspace(
        string $contentObjectClassName, WorkspaceInterface $workspace, ConditionFilterRenderer $filterConditionRenderer
    ): int
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

    public function getContentObjectRepository(): ContentObjectRepository
    {
        return $this->contentObjectRepository;
    }

    /**
     * @param class-string<\Chamilo\Core\Repository\Storage\DataClass\ContentObject> $contentObjectClassName
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param \Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer $filterConditionRenderer
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     */
    public function getContentObjectsByTypeForWorkspace(
        string $contentObjectClassName, WorkspaceInterface $workspace, ConditionFilterRenderer $filterConditionRenderer,
        ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $contentObjectClassName = empty($contentObjectClassName) ? ContentObject::class : $contentObjectClassName;

        if ($workspace instanceof PersonalWorkspace)
        {
            return $this->getContentObjectRepository()->findAllInPersonalWorkspace(
                $contentObjectClassName, $workspace, $filterConditionRenderer, $count, $offset, $orderBy
            );
        }
        else
        {
            return $this->getContentObjectRepository()->findAllInWorkspace(
                $contentObjectClassName, $workspace, $filterConditionRenderer, $count, $offset, $orderBy
            );
        }
    }
}