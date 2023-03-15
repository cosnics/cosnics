<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectRelationRepository
{
    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countContentObjectInWorkspace(
        ContentObject $contentObject, WorkspaceInterface $workspaceImplementation
    ): int
    {
        $relationConditions = [];

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObject->get_object_number())
        );
        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspaceImplementation->getId())
        );

        $relationCondition = new AndCondition($relationConditions);

        return $this->getDataClassRepository()->count(
            WorkspaceContentObjectRelation::class, new DataClassCountParameters($relationCondition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation): bool
    {
        return $this->getDataClassRepository()->create($contentObjectRelation);
    }

    public function deleteContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation): bool
    {
        return $this->getDataClassRepository()->delete($contentObjectRelation);
    }

    public function findContentObjectRelationForWorkspaceAndContentObject(
        Workspace $workspace, ContentObject $contentObject
    ): ?WorkspaceContentObjectRelation
    {
        $relationConditions = [];

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObject->get_object_number())
        );

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspace->getId())
        );

        $relationCondition = new AndCondition($relationConditions);

        return $this->getDataClassRepository()->retrieve(
            WorkspaceContentObjectRelation::class, new DataClassRetrieveParameters($relationCondition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findContentObjectRelationsForContentObject(ContentObject $contentObject): ArrayCollection
    {
        return $this->findContentObjectRelationsForContentObjectById($contentObject->getId());
    }

    /**
     * @param string $contentObjectId
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findContentObjectRelationsForContentObjectById(string $contentObjectId): ArrayCollection
    {
        $relationConditions = [];

        $relationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObjectId)
        );

        $relationCondition = new AndCondition($relationConditions);

        return $this->getDataClassRepository()->retrieves(
            WorkspaceContentObjectRelation::class, new DataClassRetrievesParameters($relationCondition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return string[]
     * @throws \Exception
     */
    public function findWorkspaceIdentifiersForContentObject(ContentObject $contentObject): array
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObject->get_object_number())
        );

        return $this->getDataClassRepository()->distinct(
            WorkspaceContentObjectRelation::class, new DataClassDistinctParameters(
                $condition, new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
                        )
                    ]
                )
            )
        );
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation): bool
    {
        return $this->getDataClassRepository()->update($contentObjectRelation);
    }
}