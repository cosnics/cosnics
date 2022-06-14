<?php
namespace Chamilo\Application\Weblcms\Request\Rights;

use Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Application\Weblcms\Request\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

class Rights extends RightsUtil
{
    const VIEW_RIGHT = '1';

    private static $instance;

    private static $target_users;

    private static $authorized_users;

    function create_request_root()
    {
        return parent::create_location(Manager::context());
    }

    /**
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            DataClassRepositoryCache::class
        );
    }

    /**
     *
     * @return \application\weblcms\request\rights\Rights
     */
    static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    function get_authorized_users(User $user)
    {
        if (!isset(self::$authorized_users[$user->get_id()]))
        {
            $location_entity_right_ids = [];
            $user_group_ids = $user->get_groups(true);

            foreach ($user_group_ids as $user_group_id)
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRightGroup::class, RightsLocationEntityRightGroup::PROPERTY_GROUP_ID
                    ), new StaticConditionVariable($user_group_id)
                );
                $right_groups = DataManager::retrieves(
                    RightsLocationEntityRightGroup::class, new DataClassRetrievesParameters($condition)
                );

                foreach ($right_groups as $right_group)
                {
                    if (!in_array($right_group->get_location_entity_right_id(), $location_entity_right_ids))
                    {
                        $location_entity_right_ids[] = $right_group->get_location_entity_right_id();
                    }
                }
            }

            $user_ids = [];

            if (count($location_entity_right_ids) > 0)
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRight::class, RightsLocationEntityRight::PROPERTY_ID
                    ), $location_entity_right_ids
                );
                $location_entity_rights = \Chamilo\Core\Rights\Storage\DataManager::retrieve_rights_location_rights(
                    Manager::context(), $condition
                );

                foreach ($location_entity_rights as $location_entity_right)
                {
                    switch ($location_entity_right->get_entity_type())
                    {
                        case UserEntity::ENTITY_TYPE :
                            if (!in_array($location_entity_right->get_entity_id(), $user_ids))
                            {
                                $user_ids[] = $location_entity_right->get_entity_id();
                            }
                            break;
                        case PlatformGroupEntity::ENTITY_TYPE :
                            $group =
                                DataManager::getInstance()->retrieve_group($location_entity_right->get_entity_id());

                            if ($group instanceof Group)
                            {
                                $group_user_ids = $group->get_users(true, true);

                                foreach ($group_user_ids as $group_user_id)
                                {
                                    if (!in_array($group_user_id, $user_ids))
                                    {
                                        $user_ids[] = $group_user_id;
                                    }
                                }
                            }
                            break;
                    }
                }
            }

            if (count($user_ids) > 0)
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(
                        User::class, User::PROPERTY_ID
                    ), $user_ids
                );
                $authorized_user_count = \Chamilo\Core\User\Storage\DataManager::count(
                    User::class, new DataClassCountParameters($condition)
                );

                if ($authorized_user_count == 0)
                {
                    $condition = new InCondition(
                        new PropertyConditionVariable(
                            User::class, User::PROPERTY_PLATFORMADMIN
                        ), 1
                    );
                }
            }
            else
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(
                        User::class, User::PROPERTY_PLATFORMADMIN
                    ), 1
                );
            }
            $authorized_users = \Chamilo\Core\User\Storage\DataManager::retrieves(
                User::class, new DataClassRetrievesParameters($condition)
            );

            foreach ($authorized_users as $authorized_user)
            {
                self::$authorized_users[$user->get_id()][] = $authorized_user;
            }
        }

        return self::$authorized_users[$user->get_id()];
    }

    static function get_available_rights()
    {
        return array(Translation::get('ViewRight') => self::VIEW_RIGHT);
    }

    function get_request_location_entity_right($entity_id, $entity_type)
    {
        return \Chamilo\Core\Rights\Storage\DataManager::retrieve_rights_location_entity_right(
            Manager::context(), self::VIEW_RIGHT, $entity_id, $entity_type, $this->get_request_root_id()
        );
    }

    function get_request_root()
    {
        return parent::get_root(Manager::context());
    }

    function get_request_root_id()
    {
        return parent::get_root_id(Manager::context());
    }

    function get_request_targets_entities()
    {
        return parent::get_target_entities(self::VIEW_RIGHT, Manager::context());
    }

    function get_request_view_rights_location_entity_right($entity_id, $entity_type)
    {
        return parent::get_rights_location_entity_right(
            Manager::context(), self::VIEW_RIGHT, $entity_id, $entity_type, self::get_request_root_id()
        );
    }

    function get_target_users(User $user)
    {
        if (!isset(self::$target_users[$user->get_id()]))
        {
            $allowed_groups = [];

            $location_entity_right = $this->get_request_location_entity_right(
                $user->get_id(), UserEntity::ENTITY_TYPE
            );
            if ($location_entity_right instanceof RightsLocationEntityRight)
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        RightsLocationEntityRightGroup::class,
                        RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
                    ), new StaticConditionVariable($location_entity_right->get_id())
                );
                $right_groups = DataManager::retrieves(
                    RightsLocationEntityRightGroup::class, new DataClassRetrievesParameters($condition)
                );

                foreach ($right_groups as $right_group)
                {
                    if (!in_array($right_group->get_group_id(), $allowed_groups))
                    {
                        $allowed_groups[] = $right_group->get_group_id();
                    }
                }
            }

            $user_group_ids = $user->get_groups(true);

            foreach ($user_group_ids as $user_group_id)
            {
                $location_entity_right = $this->get_request_location_entity_right(
                    $user_group_id, PlatformGroupEntity::ENTITY_TYPE
                );
                if ($location_entity_right instanceof RightsLocationEntityRight)
                {
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            RightsLocationEntityRightGroup::class,
                            RightsLocationEntityRightGroup::PROPERTY_LOCATION_ENTITY_RIGHT_ID
                        ), new StaticConditionVariable($location_entity_right->get_id())
                    );
                    $right_groups = DataManager::retrieves(
                        RightsLocationEntityRightGroup::class, new DataClassRetrievesParameters($condition)
                    );

                    foreach ($right_groups as $right_group)
                    {
                        if (!in_array($right_group->get_group_id(), $allowed_groups))
                        {
                            $allowed_groups[] = $right_group->get_group_id();
                        }
                    }
                }
            }

            self::$target_users[$user->get_id()] = [];

            $this->getDataClassRepositoryCache()->truncate(Group::class);

            if (count($allowed_groups) > 0)
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(
                        Group::class, Group::PROPERTY_ID
                    ), $allowed_groups
                );
                $groups = DataManager::retrieves(
                    Group::class, new DataClassRetrievesParameters($condition)
                );

                foreach ($groups as $group)
                {

                    $user_ids = $group->get_users(true, true);

                    foreach ($user_ids as $user_id)
                    {
                        if (!in_array($user_id, self::$target_users[$user->get_id()]))
                        {
                            self::$target_users[$user->get_id()][] = $user_id;
                        }
                    }
                }
            }
        }

        return self::$target_users[$user->get_id()];
    }

    function invert_request_location_entity_right($right_id, $entity_id, $entity_type)
    {
        return parent::invert_location_entity_right(
            Manager::context(), $right_id, $entity_id, $entity_type, self::get_request_root_id()
        );
    }

    function is_target_user(User $user, $target_user_id)
    {
        return in_array($target_user_id, $this->get_target_users($user));
    }

    function request_is_allowed()
    {
        $entities = [];
        $entities[UserEntity::ENTITY_TYPE] = new UserEntity();
        $entities[PlatformGroupEntity::ENTITY_TYPE] = new PlatformGroupEntity();

        return parent::is_allowed(
            self::VIEW_RIGHT, Manager::context(), null, $entities, 0, self::TYPE_ROOT, 0, self::TREE_TYPE_ROOT
        );
    }
}