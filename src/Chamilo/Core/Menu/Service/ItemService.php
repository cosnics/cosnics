<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 *
 * @package Chamilo\Core\Menu\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemService
{

    /**
     *
     * @var \Chamilo\Core\Menu\Repository\ItemRepository
     */
    private $itemRepository;

    /**
     *
     * @param \Chamilo\Core\Menu\Repository\ItemRepository $itemRepository
     */
    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Repository\ItemRepository
     */
    public function getItemRepository()
    {
        return $this->itemRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Repository\ItemRepository $itemRepository
     */
    public function setItemRepository(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     *
     * @param integer $parentIdentifier
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getItemsByParentIdentifier($parentIdentifier)
    {
        return $this->getItemRepository()->findItemsByParentIdentifier($parentIdentifier);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function getRootItems()
    {
        return $this->getItemsByParentIdentifier(0);
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item[] $items
     * @param User $user
     * @return boolean[]
     */
    public function determineRightsForUser(User $user)
    {
        $rightsCacheService = new RightsCacheService($this);
        return $rightsCacheService->getForUser($user);
    }

    /**
     * Resets the cache for the menu items
     */
    public function resetCache()
    {
        $cacheService = new ItemsCacheService($this->itemRepository);
        $cacheService->resetCache();
    }
}