<?php
namespace Chamilo\Application\Portfolio\Favourite\Table\Favourite;

use Chamilo\Application\Portfolio\Favourite\Service\FavouriteService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * Data provider for the Favourite Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteTableDataProvider extends RecordTableDataProvider
{

    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param $condition
     * @param int $offset
     * @param int $count
     * @param OrderBy[] $orderProperty
     *
     * @return ResultSet
     */
    public function retrieve_data($condition, $offset, $count, $orderProperty = null)
    {
        return $this->getFavouriteService()->findFavouriteUsers(
            $this->get_component()->getUser(),
            $condition,
            $offset,
            $count,
            $orderProperty);
    }

    /**
     * Counts the data
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->getFavouriteService()->countFavouriteUsers($this->get_component()->getUser(), $condition);
    }

    /**
     *
     * @return FavouriteService
     */
    protected function getFavouriteService()
    {
        return $this->get_component()->getFavouriteService();
    }
}