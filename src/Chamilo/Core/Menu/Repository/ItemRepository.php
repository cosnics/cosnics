<?php
namespace Chamilo\Core\Menu\Repository;

use Chamilo\Core\Menu\Service\ItemsCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemRepository
{

    /**
     *
     * @param integer $parentIdentifier
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findItemsByParentIdentifier($parentIdentifier)
    {
        $itemsCacheService = new ItemsCacheService($this);
        $items = $itemsCacheService->getItems();
        return $items[$parentIdentifier];
    }

    /**
     */
    public function findItems()
    {
        $orderBy = array();
        $orderBy[] = new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT));
        $orderBy[] = new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_SORT));
        
        return DataManager::retrieves(Item::class_name(), new DataClassRetrievesParameters(null, null, null, $orderBy));
    }
}