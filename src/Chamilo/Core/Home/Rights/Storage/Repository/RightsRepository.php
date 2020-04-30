<?php
namespace Chamilo\Core\Home\Rights\Storage\Repository;

use Chamilo\Core\Home\Rights\Storage\DataClass\BlockTypeTargetEntity;
use Chamilo\Core\Home\Rights\Storage\DataClass\ElementTargetEntity;
use Chamilo\Core\Home\Rights\Storage\DataClass\HomeTargetEntity;
use Chamilo\Core\Home\Rights\Storage\DataManager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

/**
 * Database repository for the rights entities
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsRepository
{

    /**
     * Clears the target entities for a given element
     *
     * @param Element $element
     *
     * @return bool
     */
    public function clearTargetEntitiesForElement(Element $element)
    {
        return DataManager::deletes(
            ElementTargetEntity::class,
            $this->getElementTargetEntityConditionByElement($element));
    }

    /**
     * Clears the target entities for a given block type
     *
     * @param string $blockType
     *
     * @return bool
     */
    public function clearTargetEntitiesForBlockType($blockType)
    {
        return DataManager::deletes(
            BlockTypeTargetEntity::class,
            $this->getBlockTypeTargetEntityConditionByBlockType($blockType));
    }

    /**
     * Finds the target entities for a given element
     *
     * @param Element $element
     *
     * @return ElementTargetEntity[]
     */
    public function findTargetEntitiesForElement(Element $element)
    {
        return DataManager::retrieves(
            ElementTargetEntity::class,
            new DataClassRetrievesParameters($this->getElementTargetEntityConditionByElement($element)))->as_array();
    }

    /**
     * Finds the target entities for a given block type
     *
     * @param string $blockType
     *
     * @return BlockTypeTargetEntity[]
     */
    public function findTargetEntitiesForBlockType($blockType)
    {
        return DataManager::retrieves(
            BlockTypeTargetEntity::class,
            new DataClassRetrievesParameters($this->getBlockTypeTargetEntityConditionByBlockType($blockType)))->as_array();
    }

    /**
     * Finds the element target entities
     *
     * @return ElementTargetEntity[]
     */
    public function findElementTargetEntities()
    {
        return DataManager::retrieves(ElementTargetEntity::class)->as_array();
    }

    /**
     * Finds the block type target entities
     *
     * @return BlockTypeTargetEntity[]
     */
    public function findBlockTypeTargetEntities()
    {
        return DataManager::retrieves(BlockTypeTargetEntity::class)->as_array();
    }

    /**
     * Finds the element id's that have not been attached to any entities
     *
     * @return int[]
     */
    public function findElementIdsWithNoTargetEntities()
    {
        $targetedEntityIds = DataManager::distinct(
            ElementTargetEntity::class,
            new DataClassDistinctParameters(
                null,
                new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            ElementTargetEntity::class,
                            ElementTargetEntity::PROPERTY_ELEMENT_ID)))));

        $condition = new NotCondition(
            new InCondition(
                new PropertyConditionVariable(Element::class, Element::PROPERTY_ID),
                $targetedEntityIds));

        return DataManager::distinct(
            Element::class,
            new DataClassDistinctParameters(
                $condition,
                new DataClassProperties(array(new PropertyConditionVariable(Element::class, Element::PROPERTY_ID)))));
    }

    /**
     * Finds the element ids that have been attached to entities to which the given user belongs
     *
     * @param User $user
     *
     * @return int[]
     */
    public function findElementIdsTargetedForUser(User $user)
    {
        return DataManager::distinct(
            ElementTargetEntity::class,
            new DataClassDistinctParameters(
                $this->getTargetEntitiesConditionForUser(ElementTargetEntity::class, $user),
                new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            ElementTargetEntity::class,
                            ElementTargetEntity::PROPERTY_ELEMENT_ID)))));
    }

    /**
     * Returns the block types that have been targeted to a specific user
     *
     * @return string[]
     */
    public function findTargetedBlockTypes()
    {
        return DataManager::distinct(
            BlockTypeTargetEntity::class,
            new DataClassDistinctParameters(
                null,
                new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            BlockTypeTargetEntity::class,
                            BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE)))));
    }

    /**
     * Finds the block type classes that have been attached to entities to which the given user belongs
     *
     * @param User $user
     *
     * @return string[]
     */
    public function findBlockTypesTargetedForUser(User $user)
    {
        return DataManager::distinct(
            BlockTypeTargetEntity::class,
            new DataClassDistinctParameters(
                $this->getTargetEntitiesConditionForUser(BlockTypeTargetEntity::class, $user),
                new DataClassProperties(
                    array(
                        new PropertyConditionVariable(
                            BlockTypeTargetEntity::class,
                            BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE)))));
    }

    /**
     * Returns the conditions to retrieve target entities for a given user
     *
     * @param string $targetEntitiesClassName
     * @param User $user
     *
     * @return Condition
     */
    protected function getTargetEntitiesConditionForUser($targetEntitiesClassName, User $user)
    {
        $condition = null;

        if (! $user->is_platform_admin())
        {
            $conditions = array();

            $userConditions = array();

            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable(UserEntity::ENTITY_TYPE));

            $userConditions[] = new EqualityCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_ID),
                new StaticConditionVariable($user->getId()));

            $conditions[] = new AndCondition($userConditions);

            $groupConditions[] = new EqualityCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable(PlatformGroupEntity::ENTITY_TYPE));

            $groupConditions[] = new InCondition(
                new PropertyConditionVariable($targetEntitiesClassName, HomeTargetEntity::PROPERTY_ENTITY_ID),
                $user->get_groups(true));

            $conditions[] = new AndCondition($groupConditions);

            $condition = new OrCondition($conditions);
        }

        return $condition;
    }

    /**
     * Returns a condition for the ElementTargetEntity data class by a given element
     *
     * @param Element $element
     *
     * @return EqualityCondition
     */
    protected function getElementTargetEntityConditionByElement(Element $element)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(ElementTargetEntity::class, ElementTargetEntity::PROPERTY_ELEMENT_ID),
            new StaticConditionVariable($element->getId()));
    }

    /**
     * Returns a condition for the BlockTypeTargetEntity data class by a given block type
     *
     * @param string $blockType
     *
     * @return EqualityCondition
     */
    protected function getBlockTypeTargetEntityConditionByBlockType($blockType)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                BlockTypeTargetEntity::class,
                BlockTypeTargetEntity::PROPERTY_BLOCK_TYPE),
            new StaticConditionVariable($blockType));
    }
}