<?php
namespace Chamilo\Core\Group\Table\GroupRelUser;

use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

class GroupRelUserTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->get_component()->count_group_rel_users($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $orderBy = new OrderBy();
        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME), SORT_ASC
            )
        );
        $orderBy->add(
            new OrderProperty(
                new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME), SORT_ASC
            )
        );

        return DataManager::retrieve_group_rel_users_with_user_join(
            $condition, $offset, $count, $orderBy
        );
    }
}
