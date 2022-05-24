<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\User;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * User table data provider
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->get_component()->get_parent()->count_portfolio_possible_view_users($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->get_component()->get_parent()->retrieve_portfolio_possible_view_users(
            $condition, $count, $offset, $orderBy
        );
    }
}