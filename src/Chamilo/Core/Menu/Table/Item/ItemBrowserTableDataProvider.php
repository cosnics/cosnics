<?php
namespace Chamilo\Core\Menu\Table\Item;

use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Table\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemBrowserTableDataProvider extends DataClassTableDataProvider
{
    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var integer
     */
    private $parentIdentifier;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param integer $parentIdentifier
     */
    public function __construct($table, ItemService $itemService, int $parentIdentifier)
    {
        parent::__construct($table);
        $this->itemService = $itemService;
        $this->parentIdentifier = $parentIdentifier;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function count_data($condition)
    {
        return $this->getItemService()->countItemsByParentIdentifier($this->getParentIdentifier());
    }

    /**
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    /**
     * @return integer
     */
    public function getParentIdentifier(): int
    {
        return $this->parentIdentifier;
    }

    /**
     * @param integer $parentIdentifier
     */
    public function setParentIdentifier(int $parentIdentifier): void
    {
        $this->parentIdentifier = $parentIdentifier;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperties
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function retrieve_data($condition, $offset, $count, $orderProperties = null)
    {
        if (is_null($orderProperties))
        {
            $orderProperties = new OrderBy();
        }

        $orderProperties->add(new OrderProperty(new PropertyConditionVariable(Item::class, Item::PROPERTY_SORT)));

        return $this->getItemService()->findItemsByParentIdentifier(
            $this->getParentIdentifier(), $count, $offset, $orderProperties
        );
    }
}
