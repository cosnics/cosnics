<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataClass\NestedSet;

/**
 * @package Chamilo\Core\Group\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter De Neef
 * @author  Sven Vanpoucke
 */
class Group extends NestedSet
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CODE = 'code';
    public const PROPERTY_DATABASE_QUOTA = 'database_quota';
    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_DISK_QUOTA = 'disk_quota';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_SORT = 'sort';

    /**
     * @param bool $recursive
     *
     * @return int
     * @deprecated Use GroupsTreeTraverser::countSubGroupsForGroup() now
     */
    public function count_subgroups(bool $recursive = false): int
    {
        return $this->getGroupsTreeTraverser()->countSubGroupsForGroup($this, $recursive);
    }

    /**
     * @deprecated Use GroupsTreeTraverser::countUsersForGroup() now
     */
    public function count_users(bool $include_subgroups = false, bool $recursive_subgroups = false): int
    {
        return $this->getGroupsTreeTraverser()->countUsersForGroup($this, $include_subgroups, $recursive_subgroups);
    }

    /**
     * @param int $previous_id
     *
     * @return bool
     * @throws \Throwable
     * @deprecated Use GroupService::createGroup() now
     */
    public function create($previous_id = 0, $reference_node = null): bool
    {
        $parent_id = $this->getParentId();

        if ($previous_id)
        {
            return parent::create(parent::AS_NEXT_SIBLING_OF, $previous_id);
        }
        else
        {
            return parent::create(parent::AS_LAST_CHILD_OF, $parent_id);
        }
    }

    /**
     * Instructs the DataManager to delete this group.
     *
     * @param $in_batch - delete groups in batch and fix nested values later
     *
     * @return bool True if success, false otherwise.
     * @throws \Throwable
     * @deprecated should use $this->delete() of self::deletes( $array ) instead
     */
    public function delete_group($in_batch = false): bool
    {
        return self::delete();
    }

    /**
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_NAME,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_SORT,
                self::PROPERTY_CODE,
                self::PROPERTY_DISK_QUOTA,
                self::PROPERTY_DATABASE_QUOTA
            ]
        );
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(GroupsTreeTraverser::class);
    }

    public static function getStorageUnitName(): string
    {
        return 'group_group';
    }

    public function get_code(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_CODE);
    }

    public function get_database_quota(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_DATABASE_QUOTA);
    }

    public function get_description(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    public function get_disk_quota(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_DISK_QUOTA);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use GroupsTreeTraverser::getFullyQualifiedNameForGroup() now
     */
    public function get_fully_qualified_name(bool $include_self = true): string
    {
        return $this->getGroupsTreeTraverser()->getFullyQualifiedNameForGroup($this, $include_self);
    }

    public function get_name(): ?string
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * Get all of the group's parents
     *
     * @deprecated should use get_ancestors() instead
     */
    public function get_parents($include_self = true)
    {
        return $this->get_ancestors($include_self);
    }

    public function get_sort(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_SORT);
    }

    /**
     * @return \Chamilo\Core\Group\Storage\DataClass\Group[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @deprecated Use GroupsTreeTraverser::findSubGroupsForGroup() now
     */
    public function get_subgroups(bool $recursive = false): array
    {
        return $this->getGroupsTreeTraverser()->findSubGroupsForGroup($this, $recursive);
    }

    /**
     * @param bool $include_subgroups
     * @param bool $recursive_subgroups
     *
     * @return string[]
     * @deprecated Use GroupsTreeTraverser::findUserIdentifiersForGroup() now
     */
    public function get_users(bool $include_subgroups = false, bool $recursive_subgroups = false): array
    {
        return $this->getGroupsTreeTraverser()->findUserIdentifiersForGroup(
            $this, $include_subgroups, $recursive_subgroups
        );
    }

    /**
     * @deprecated should use is_descendant_of
     */
    public function is_child_of(Group $parent): bool
    {
        return $this->isDescendantOf($parent);
    }

    /**
     * @deprecated Use Group::isAncestorOf()
     */
    public function is_parent_of(Group $child): bool
    {
        return $this->isAncestorOf($child);
    }

    /**
     * @return bool
     * @throws \Throwable
     * @deprecated Use GroupService::moveGroup() now
     */
    public function move($new_parent_id = 0, $new_previous_id = null, $condition = null): bool
    {
        if ($new_previous_id != 0)
        {
            return parent::move(self::AS_NEXT_SIBLING_OF, $new_previous_id);
        }
        else
        {
            return parent::move(self::AS_LAST_CHILD_OF, $new_parent_id);
        }
    }

    public function set_code(?string $code): void
    {
        $this->setDefaultProperty(self::PROPERTY_CODE, $code);
    }

    public function set_database_quota(int $database_quota): void
    {
        $this->setDefaultProperty(self::PROPERTY_DATABASE_QUOTA, $database_quota);
    }

    public function set_description(?string $description): void
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    public function set_disk_quota(int $disk_quota): void
    {
        $this->setDefaultProperty(self::PROPERTY_DISK_QUOTA, $disk_quota);
    }

    public function set_name(?string $name): void
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * @deprecated use setParentId() || move instead.
     */
    public function set_parent(string $parent): void
    {
        $this->setParentId($parent);
    }

    public function set_sort(int $sort): void
    {
        $this->setDefaultProperty(self::PROPERTY_SORT, $sort);
    }
}
