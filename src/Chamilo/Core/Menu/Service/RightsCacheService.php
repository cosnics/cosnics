<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsCacheService
{

    private AdapterInterface $cacheAdapter;

    private ItemService $itemService;

    private RightsService $rightsService;

    public function __construct(RightsService $rightsService, ItemService $itemService, AdapterInterface $cacheAdapter)
    {
        $this->rightsService = $rightsService;
        $this->itemService = $itemService;
        $this->cacheAdapter = $cacheAdapter;
    }

    public function canUserViewItem(User $user, Item $item): bool
    {
        if (!$item->isIdentified())
        {
            return true;
        }

        $cacheAdapter = $this->getCacheAdapter();

        try
        {
            $cacheItem = $cacheAdapter->getItem($user->getId());

            if (!$cacheItem->isHit())
            {
                $cacheItem->set($this->getUserRightsForAllItems($user));
                $cacheAdapter->save($cacheItem);
            }

            $userRights = $cacheItem->get();

            return $userRights[$item->getId()];
        }
        catch (Exception|InvalidArgumentException $exception)
        {
            return false;
        }
    }

    public function clear(): bool
    {
        return $this->getCacheAdapter()->clear();
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->cacheAdapter;
    }

    public function getItemService(): ItemService
    {
        return $this->itemService;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @return bool[]
     * @throws \Exception
     */
    protected function getUserRightsForAllItems(User $user): array
    {
        $items = $this->getItemService()->findItems();
        $itemRights = [];

        foreach ($items as $item)
        {
            $itemRights[$item->getId()] = $this->getRightsService()->canUserViewItem($user, $item);
        }

        return $itemRights;
    }

    public function setCacheAdapter(AdapterInterface $cacheAdapter): void
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    public function setItemService(ItemService $itemService): void
    {
        $this->itemService = $itemService;
    }

    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }
}