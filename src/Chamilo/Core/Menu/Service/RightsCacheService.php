<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Traits\SingleCacheAdapterHandlerTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsCacheService
{
    use SingleCacheAdapterHandlerTrait;

    private ItemService $itemService;

    private RightsService $rightsService;

    public function __construct(RightsService $rightsService, ItemService $itemService, AdapterInterface $cacheAdapter)
    {
        $this->rightsService = $rightsService;
        $this->itemService = $itemService;
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function canUserViewItem(User $user, Item $item): bool
    {
        if (!$item->isIdentified())
        {
            return true;
        }

        $userRights = $this->getUserRightsForAllItems($user);

        return $userRights[$item->getId()];
    }

    public function clear(): bool
    {
        return $this->clearAllCacheData();
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
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Exception
     */
    protected function getUserRightsForAllItems(User $user): array
    {
        $cacheIdentifier = $this->getCacheKeyForParts([__CLASS__, __METHOD__, $user->getId()]);

        if (!$this->hasCacheDataForKey($cacheIdentifier))
        {
            $items = $this->getItemService()->findItems();
            $itemRights = [];

            foreach ($items as $item)
            {
                $itemRights[$item->getId()] = $this->getRightsService()->canUserViewItem($user, $item);
            }

            $this->saveCacheDataForKey($cacheIdentifier, $itemRights);
        }

        return $this->readCacheDataForKey($cacheIdentifier);
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