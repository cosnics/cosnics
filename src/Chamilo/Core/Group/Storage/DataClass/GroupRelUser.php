<?php
namespace Chamilo\Core\Group\Storage\DataClass;

use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package group.lib
 */

/**
 *
 * @author Hans de Bisschop
 * @author Dieter De Neef
 */
class GroupRelUser extends DataClass
{
    const PROPERTY_GROUP_ID = 'group_id';
    const PROPERTY_USER_ID = 'user_id';

    public function delete()
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_GROUP_ID),
            new StaticConditionVariable($this->get_group_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(GroupRelUser::class, GroupRelUser::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );

        $condition = new AndCondition($conditions);

        return DataManager::deletes(GroupRelUser::class, $condition);
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties of all groups.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = [])
    {
        return parent::get_default_property_names(array(self::PROPERTY_GROUP_ID, self::PROPERTY_USER_ID));
    }

    public function get_group_id()
    {
        return $this->get_default_property(self::PROPERTY_GROUP_ID);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'group_group_rel_user';
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_group_id($group_id)
    {
        $this->set_default_property(self::PROPERTY_GROUP_ID, $group_id);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }
}
