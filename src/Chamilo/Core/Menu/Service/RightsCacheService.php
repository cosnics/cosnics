<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;

/**
 * @package Chamilo\Core\Menu\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsCacheService
{

    /**
     * @var \Chamilo\Core\Menu\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Chamilo\Core\Menu\Service\ItemService
     */
    private $itemService;

    /**
     * @var \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    private $cacheProvider;

    /**
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     * @param \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache $cacheProvider
     */
    public function __construct(RightsService $rightsService, ItemService $itemService, FilesystemCache $cacheProvider)
    {
        $this->rightsService = $rightsService;
        $this->itemService = $itemService;
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return boolean
     * @throws \Exception
     */
    public function canUserViewItem(User $user, Item $item)
    {
        if (!$item->isIdentified())
        {
            return true;
        }

        $cacheProvider = $this->getCacheProvider();

        if (!$cacheProvider->contains($user->getId()))
        {
            $cacheProvider->save($user->getId(), $this->getUserRightsForAllItems($user));
        }

        $userRights = $cacheProvider->fetch($user->getId());

        return $userRights[$item->getId()];
    }

    /**
     * @return boolean
     */
    public function clear()
    {
        return $this->getCacheProvider()->deleteAll();
    }

    /**
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    public function getCacheProvider(): FilesystemCache
    {
        return $this->cacheProvider;
    }

    /**
     * @param \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache $cacheProvider
     */
    public function setCacheProvider(FilesystemCache $cacheProvider): void
    {
        $this->cacheProvider = $cacheProvider;
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
     * @return \Chamilo\Core\Menu\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Menu\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean[]
     * @throws \Exception
     */
    protected function getUserRightsForAllItems(User $user)
    {
        $items = $this->getItemService()->findItems();
        $itemRights = [];

        foreach ($items as $item)
        {
            $itemRights[$item->getId()] = $this->getRightsService()->canUserViewItem($user, $item);
        }

        return $itemRights;
    }
}