<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Subscribed;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Data provider for a direct subscribed course user browser table, or users
 * in a direct subscribed group.
 *
 * @author Stijn Van Hoecke
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to RecordTable
 */
class SubscribedUserTableDataProvider extends RecordTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return DataManager::count_users_directly_subscribed_to_course(
            $condition
        );
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return DataManager::retrieve_users_directly_subscribed_to_course(
            $condition, $offset, $count, $orderBy
        );
    }
}
