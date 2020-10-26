<?php
namespace Chamilo\Core\Group\Table\GroupRelUser;

use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class GroupRelUserTableDataProvider extends DataClassTableDataProvider
{

    public function count_data($condition)
    {
        return $this->get_component()->count_group_rel_users($condition);
    }

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $order_property = array();
        $order_property[] = new OrderBy(
            new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), SORT_ASC
        );
        $order_property[] = new OrderBy(
            new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), SORT_ASC
        );

        return DataManager::retrieve_group_rel_users_with_user_join($condition, $offset, $count, $order_property);
    }
}
