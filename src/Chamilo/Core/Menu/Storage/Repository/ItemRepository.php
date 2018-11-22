<?php
namespace Chamilo\Core\Menu\Storage\Repository;

use Chamilo\Core\Menu\Service\ItemsCacheService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Core\Menu\Storage\Repository
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemRepository
{

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    private $dataClassRepository;

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     */
    public function createItem(Item $item)
    {
        return $this->getDataClassRepository()->create($item);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\ItemTitle $itemTitle
     *
     * @return boolean
     */
    public function createItemTitle(ItemTitle $itemTitle)
    {
        return $this->getDataClassRepository()->create($itemTitle);
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItems()
    {
        $orderBy = array();
        $orderBy[] = new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_PARENT));
        $orderBy[] = new OrderBy(new PropertyConditionVariable(Item::class_name(), Item::PROPERTY_SORT));

        return $this->getDataClassRepository()->retrieves(
            Item::class_name(), new DataClassRetrievesParameters(null, null, null, $orderBy)
        );
    }

    /**
     *
     * @param integer $parentIdentifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function findItemsByParentIdentifier($parentIdentifier)
    {
        $itemsCacheService = new ItemsCacheService($this);
        $items = $itemsCacheService->getItems();

        return $items[$parentIdentifier];
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->dataClassRepository;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    protected function setDataClassRepository(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }
}