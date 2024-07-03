<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\StorageParameters;
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
            WorkspaceContentObjectRelation::class, new StorageParameters(condition: $relationCondition)
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
            Workspace::class, new StorageParameters(condition: $condition, joins: new Joins([$join]))
        );
    }

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
            WorkspaceContentObjectRelation::class, new StorageParameters(condition: $relationCondition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation>
     */
    public function findContentObjectRelationsForContentObject(ContentObject $contentObject): ArrayCollection
    {
        return $this->findContentObjectRelationsForContentObjectById($contentObject->getId());
    }

    /**
     * @param string $contentObjectId
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation>
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
            WorkspaceContentObjectRelation::class, new StorageParameters(condition: $relationCondition)
        );
    }

    public function findWorkspaceAndRelationForContentObjectIdentifier(
        string $contentObjectNumber, ?int $limit = null, ?int $count = null, OrderBy $orderBy = new OrderBy()
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
            Workspace::class, new StorageParameters(
                condition: $condition, joins: new Joins([$join]), retrieveProperties: $retrieveProperties,
                orderBy: $orderBy, count: $count, offset: $limit
            )
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
            WorkspaceContentObjectRelation::class, new StorageParameters(
                condition: $condition, retrieveProperties: new RetrieveProperties(
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

    public function updateContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation): bool
    {
        return $this->getDataClassRepository()->update($contentObjectRelation);
    }
}