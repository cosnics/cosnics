<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
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
        ContentObject $contentObject, Workspace $workspaceImplementation
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

    public function countWorkspaceAndRelationForContentObjectIdentifier(string $contentObjectNumber): int
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObjectNumber)
        );

        $join = new Join(
            WorkspaceContentObjectRelation::class, new EqualityCondition(
                new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID), new PropertyConditionVariable(
                    WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
                )
            )
        );

        return $this->getDataClassRepository()->count(
            Workspace::class, new DataClassCountParameters($condition, new Joins([$join]))
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findWorkspaceAndRelationForContentObjectIdentifier(
        string $contentObjectNumber, ?int $limit = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
            ), new StaticConditionVariable($contentObjectNumber)
        );

        $retrieveProperties = new RetrieveProperties(
            [
                new PropertyConditionVariable(WorkspaceContentObjectRelation::class, DataClass::PROPERTY_ID),
                new PropertyConditionVariable(
                    WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CONTENT_OBJECT_ID
                ),
                new PropertyConditionVariable(
                    WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_CATEGORY_ID
                ),
                new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID),
                new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_NAME),
                new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_DESCRIPTION),
                new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_CREATOR_ID),
                new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_CREATION_DATE)
            ]
        );

        $join = new Join(
            WorkspaceContentObjectRelation::class, new EqualityCondition(
                new PropertyConditionVariable(Workspace::class, DataClass::PROPERTY_ID), new PropertyConditionVariable(
                    WorkspaceContentObjectRelation::class, WorkspaceContentObjectRelation::PROPERTY_WORKSPACE_ID
                )
            )
        );

        return $this->getDataClassRepository()->records(
            Workspace::class,
            new RecordRetrievesParameters($retrieveProperties, $condition, $count, $limit, $orderBy, new Joins([$join]))
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return string[]
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