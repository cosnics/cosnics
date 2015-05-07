<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationService
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository
     */
    private $entityRelationRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository $entityRelationRepository
     */
    public function __construct(EntityRelationRepository $entityRelationRepository)
    {
        $this->entityRelationRepository = $entityRelationRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository
     */
    public function getEntityRelationRepository()
    {
        return $this->entityRelationRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository $entityRelationRepository
     */
    public function setEntityRelationRepository($entityRelationRepository)
    {
        $this->entityRelationRepository = $entityRelationRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $right
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function hasRight(User $user, $right, ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation)
    {
        return $this->getEntityRelationRepository()->findUserWithRight($user, $right, $contentObject);
    }
}