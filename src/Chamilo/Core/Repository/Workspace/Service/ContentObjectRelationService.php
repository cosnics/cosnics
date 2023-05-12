<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Service\ContentObjectService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use RuntimeException;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectRelationService
{
    protected ContentObjectRelationRepository $contentObjectRelationRepository;

    protected ContentObjectService $contentObjectService;

    protected WorkspaceService $workspaceService;

    public function __construct(
        ContentObjectService $contentObjectService, WorkspaceService $workspaceService,
        ContentObjectRelationRepository $contentObjectRelationRepository
    )
    {
        $this->contentObjectService = $contentObjectService;
        $this->workspaceService = $workspaceService;
        $this->contentObjectRelationRepository = $contentObjectRelationRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function countAvailableWorkspacesForContentObjectIdentifiersAndUser(
        array $contentObjectIdentifiers, User $user
    ): int
    {
        $contentObjects =
            $this->getContentObjectService()->retrieveContentObjectsByIdentifiers($contentObjectIdentifiers);

        return $this->countAvailableWorkspacesForContentObjectsAndUser($contentObjects, $user);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject> $contentObjects
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function countAvailableWorkspacesForContentObjectsAndUser(ArrayCollection $contentObjects, User $user): int
    {
        $workspaceIdentifiers = [];

        foreach ($contentObjects as $contentObject)
        {
            $workspaceIdentifiers = array_merge(
                $workspaceIdentifiers, $this->getWorkspaceIdentifiersForContentObject($contentObject)
            );
        }

        return $this->getWorkspaceService()->countWorkspacesForUserWithExcludedWorkspaces(
            $user, RightsService::RIGHT_ADD, $workspaceIdentifiers
        );
    }

    public function countContentObjectInWorkspace(
        ContentObject $contentObject, Workspace $workspaceImplementation
    ): int
    {
        return $this->getContentObjectRelationRepository()->countContentObjectInWorkspace(
            $contentObject, $workspaceImplementation
        );
    }

    public function countWorkspaceAndRelationForContentObject(ContentObject $contentObject): int
    {
        return $this->countWorkspaceAndRelationForContentObjectNumber($contentObject->get_object_number());
    }

    public function countWorkspaceAndRelationForContentObjectNumber(string $contentObjectNumber): int
    {
        return $this->getContentObjectRelationRepository()->countWorkspaceAndRelationForContentObjectIdentifier(
            $contentObjectNumber
        );
    }

    /**
     * @throws \Exception
     */
    public function countWorkspacesForContentObject(ContentObject $contentObject): int
    {
        return count($this->getWorkspaceIdentifiersForContentObject($contentObject));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation): bool
    {
        return $this->getContentObjectRelationRepository()->createContentObjectRelation($contentObjectRelation);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createContentObjectRelationFromParameters(
        string $workspaceId, string $contentObjectId, string $categoryId
    ): ?WorkspaceContentObjectRelation
    {
        $contentObjectRelation = new WorkspaceContentObjectRelation();

        $this->setContentObjectRelationProperties($contentObjectRelation, $workspaceId, $contentObjectId, $categoryId);

        if (!$this->createContentObjectRelation($contentObjectRelation))
        {
            return null;
        }

        return $contentObjectRelation;
    }

    public function deleteContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation): bool
    {
        return $this->getContentObjectRelationRepository()->deleteContentObjectRelation($contentObjectRelation);
    }

    public function deleteContentObjectRelationByWorkspaceAndContentObject(
        Workspace $workspace, ContentObject $contentObject
    ): bool
    {
        $contentObjectRelation =
            $this->getContentObjectRelationForWorkspaceAndContentObject($workspace, $contentObject);

        return $this->deleteContentObjectRelation($contentObjectRelation);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAvailableWorkspacesForContentObjectIdentifiersAndUser(
        array $contentObjectIdentifiers, User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $contentObjects =
            $this->getContentObjectService()->retrieveContentObjectsByIdentifiers($contentObjectIdentifiers);

        return $this->getAvailableWorkspacesForContentObjectsAndUser($contentObjects, $user, $limit, $offset, $orderBy);
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject> $contentObjects
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAvailableWorkspacesForContentObjectsAndUser(
        ArrayCollection $contentObjects, User $user, ?int $limit = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $workspaceIdentifiers = [];

        foreach ($contentObjects as $contentObject)
        {
            $workspaceIdentifiers = array_merge(
                $workspaceIdentifiers, $this->getWorkspaceIdentifiersForContentObject($contentObject)
            );
        }

        return $this->getWorkspaceService()->getWorkspacesForUserWithExcludedWorkspaces(
            $user, RightsService::RIGHT_ADD, $workspaceIdentifiers, $limit, $offset, $orderBy
        );
    }

    public function getContentObjectRelationForWorkspaceAndContentObject(
        Workspace $workspace, ContentObject $contentObject
    ): WorkspaceContentObjectRelation
    {
        return $this->getContentObjectRelationRepository()->findContentObjectRelationForWorkspaceAndContentObject(
            $workspace, $contentObject
        );
    }

    public function getContentObjectRelationRepository(): ContentObjectRelationRepository
    {
        return $this->contentObjectRelationRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getContentObjectRelationsForContentObject(ContentObject $contentObject): ArrayCollection
    {
        return $this->getContentObjectRelationRepository()->findContentObjectRelationsForContentObject($contentObject);
    }

    public function getContentObjectService(): ContentObjectService
    {
        return $this->contentObjectService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspaceAndRelationForContentObject(
        ContentObject $contentObject, ?int $limit = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getWorkspaceAndRelationForContentObjectNumber(
            $contentObject->get_object_number(), $limit, $count, $orderBy
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspaceAndRelationForContentObjectNumber(
        string $contentObjectNumber, ?int $limit = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getContentObjectRelationRepository()->findWorkspaceAndRelationForContentObjectIdentifier(
            $contentObjectNumber, $limit, $count, $orderBy
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return string[]
     */
    public function getWorkspaceIdentifiersForContentObject(ContentObject $contentObject): array
    {
        return $this->getContentObjectRelationRepository()->findWorkspaceIdentifiersForContentObject($contentObject);
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->workspaceService;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getWorkspacesForContentObject(ContentObject $contentObject): ArrayCollection
    {
        return $this->getWorkspaceService()->getWorkspacesByIdentifiers(
            $this->getWorkspaceIdentifiersForContentObject($contentObject)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    public function isContentObjectInWorkspace(ContentObject $contentObject, Workspace $workspaceImplementation): bool
    {
        return $this->countContentObjectInWorkspace($contentObject, $workspaceImplementation) > 0;
    }

    private function setContentObjectRelationProperties(
        WorkspaceContentObjectRelation $contentObjectRelation, string $workspaceId, string $contentObjectId,
        string $categoryId
    )
    {
        $contentObjectRelation->setWorkspaceId($workspaceId);
        $contentObjectRelation->setContentObjectId($contentObjectId);
        $contentObjectRelation->setCategoryId($categoryId);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateContentObjectIdInAllWorkspaces(string $oldContentObjectId, string $newContentObjectId)
    {
        $contentObjectRelations =
            $this->getContentObjectRelationRepository()->findContentObjectRelationsForContentObjectById(
                $oldContentObjectId
            );

        foreach ($contentObjectRelations as $contentObjectRelation)
        {
            $contentObjectRelation->setContentObjectId($newContentObjectId);

            if (!$this->updateContentObjectRelation($contentObjectRelation))
            {
                throw new RuntimeException(
                    sprintf(
                        'Could not update the WorkspaceContentObjectRelation object with id %s',
                        $contentObjectRelation->getId()
                    )
                );
            }
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation): bool
    {
        return $this->getContentObjectRelationRepository()->updateContentObjectRelation($contentObjectRelation);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateContentObjectRelationFromParameters(
        WorkspaceContentObjectRelation $contentObjectRelation, string $workspaceId, string $contentObjectId,
        string $categoryId
    ): bool
    {
        $this->setContentObjectRelationProperties($contentObjectRelation, $workspaceId, $contentObjectId, $categoryId);

        return $this->updateContentObjectRelation($contentObjectRelation);
    }
}