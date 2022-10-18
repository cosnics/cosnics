<?php
namespace Chamilo\Application\Portfolio\Favourite\Table\Favourite;

use Chamilo\Application\Portfolio\Favourite\Service\FavouriteService;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Data provider for the Favourite Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FavouriteTableDataProvider extends RecordTableDataProvider
{

    public function countData(?Condition $condition = null): int
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

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getFavouriteService()->findFavouriteUsers(
            $this->get_component()->getUser(), $condition, $offset, $count, $orderBy
        );
    }
}