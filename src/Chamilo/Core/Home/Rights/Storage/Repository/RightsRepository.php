<?php
namespace Chamilo\Core\Home\Rights\Storage\Repository;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity;
use Chamilo\Core\Home\Rights\Storage\DataClass\HomeTargetEntity;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Home\Rights\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsRepository
{
    protected DataClassRepository $dataClassRepository;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    public function __construct(DataClassRepository $dataClassRepository, GroupsTreeTraverser $groupsTreeTraverser)
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    public function clearTargetEntitiesForBlockType(string $blockType): bool
    {
        return $this->getDataClassRepository()->deletes(
            BlockTypeTargetEntity::class, $this->getBlockTypeTargetEntityConditionByBlockType($blockType)
        );
    }

    public function clearTargetEntitiesForElement(Element $element): bool
    {
        return $this->getDataClassRepository()->deletes(
            ElementTargetEntity::class, $this->getElementTargetEntityConditionByElement($element)
        );
    }

    public function createBlockTypeTargetEntity(BlockTypeTargetEntity $blockTypeTargetEntity): bool
    {
        return $this->getDataClassRepository()->create($blockTypeTargetEntity);
    }

    public function createElementTargetEntity(ElementTargetEntity $elementTargetEntity): bool
    {
        return $this->getDataClassRepository()->create($elementTargetEntity);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity>
     */
    public function findBlockTypeTargetEntities(): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            BlockTypeTargetEntity::class, new StorageParameters()
        );
    }

    /**
     * @return string[]
     */
    public function findBlockTypesTargetedForUser(User $user): array
    {
        return $this->getDataClassRepository()->distinct(
            BlockTypeTargetEntity::class, new StorageParameters(
                condition: $this->getTargetEntitiesConditionForUser(BlockTypeTargetEntity::class, $user),
                retrieveProperties: new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            BlockTypeTargetEntity::class, BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE
                        )
                    ]
                )
            )
        );
    }

    /**
     * @return int[]
     */
    public function findElementIdsTargetedForUser(User $user): array
    {
        return $this->getDataClassRepository()->distinct(
            ElementTargetEntity::class, new StorageParameters(
                condition: $this->getTargetEntitiesConditionForUser(ElementTargetEntity::class, $user),
                retrieveProperties: new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            ElementTargetEntity::class, ElementTargetEntity::PROPERTY_ELEMENT_ID
                        )
                    ]
                )
            )
        );
    }

    /**
     * @return int[]
     */
    public function findElementIdsWithNoTargetEntities(): array
    {
        $targetedEntityIds = $this->getDataClassRepository()->distinct(
            ElementTargetEntity::class, new StorageParameters(
                retrieveProperties: new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            ElementTargetEntity::class, ElementTargetEntity::PROPERTY_ELEMENT_ID
                        )
                    ]
                )
            )
        );

        $condition = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID), $targetedEntityIds
            )
        );

        return $this->getDataClassRepository()->distinct(
            Element::class, new StorageParameters(
                condition: $condition, retrieveProperties: new RetrieveProperties(
                [new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID)]
            )
            )
        );
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity>
     */
    public function findElementTargetEntities(): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            ElementTargetEntity::class, new StorageParameters()
        );
    }

    /**
     * @param string $blockType
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity>
     */
    public function findTargetEntitiesForBlockType(string $blockType): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            BlockTypeTargetEntity::class,
            new StorageParameters(condition: $this->getBlockTypeTargetEntityConditionByBlockType($blockType))
        );
    }

    /**
     * @param \Chamilo\Core\Home\Storage\DataClass\Element $element
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity>
     */
    public function findTargetEntitiesForElement(Element $element): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            ElementTargetEntity::class,
            new StorageParameters(condition: $this->getElementTargetEntityConditionByElement($element))
        );
    }

    /**
     * @return string[]
     */
    public function findTargetedBlockTypes(): array
    {
        return $this->getDataClassRepository()->distinct(
            BlockTypeTargetEntity::class, new StorageParameters(
                retrieveProperties: new RetrieveProperties(
                    [
                        new PropertyConditionVariable(
                            BlockTypeTargetEntity::class, BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE
                        )
                    ]
                )
            )
        );
    }

    protected function getBlockTypeTargetEntityConditionByBlockType(string $blockType): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                BlockTypeTargetEntity::class, BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE
            ), new StaticConditionVariable($blockType)
        );
    }

    public function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    protected function getElementTargetEntityConditionByElement(Element $element): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ElementTargetEntity::class, ElementTargetEntity::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->getId())
        );
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    protected function getTargetEntitiesConditionForUser(string $targetEntitiesClassName, User $user): ?OrCondition
    {
        $condition = null;

        if (!$user->isPlatformAdmin())
        {
            $conditions = [];

            $userConditions = [];

            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable(UserEntityProvider::ENTITY_TYPE)
            );

            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_ID),
                new StaticConditionVariable($user->getId())
            );

            $conditions[] = new AndCondition($userConditions);

            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable(GroupEntityProvider::ENTITY_TYPE)
            );

            $groupConditions[] = new InCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_ID),
                $this->getGroupsTreeTraverser()->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId())
            );

            $conditions[] = new AndCondition($groupConditions);

            $condition = new OrCondition($conditions);
        }

        return $condition;
    }
}