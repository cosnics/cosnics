<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Storage\Repository\ItemRepository;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;

/**
 *
 * @package Chamilo\Core\Menu\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemsCacheService extends DoctrineFilesystemCacheService
{
    const IDENTIFIER_ITEMS = 'items';

    /**
     *
     * @var \Chamilo\Core\Menu\Storage\Repository\ItemRepository
     */
    private $itemRepository;

    /**
     *
     * @param \Chamilo\Core\Menu\Storage\Repository\ItemRepository $itemRepository
     */
    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Storage\Repository\ItemRepository
     */
    public function getItemRepository()
    {
        return $this->itemRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Storage\Repository\ItemRepository $itemRepository
     */
    public function setItemRepository($itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param string $identifier
     *
     * @return boolean
     */
    public function warmUpForIdentifier($identifier)
    {
        $itemsByParentIdentifier = $this->processItems($this->getItemRepository()->findItems());

        return $this->getCacheProvider()->save($identifier, $itemsByParentIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item[] $items
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    private function processItems(DataClassIterator $items)
    {
        $itemsByParentIdentifier = array();

        foreach ($items as $item)
        {
            $item->get_additional_properties();
            $item->get_titles();

            if (!isset($itemsByParentIdentifier[$item->get_parent()]))
            {
                $itemsByParentIdentifier[$item->get_parent()] = array();
            }

            $itemsByParentIdentifier[$item->get_parent()][] = $item;
        }

        return $itemsByParentIdentifier;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return 'Chamilo\Core\Menu\Repository';
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return array(self::IDENTIFIER_ITEMS);
    }

    /**
     * @return false|mixed
     * @throws \Exception
     */
    public function getItems()
    {
        return $this->getForIdentifier(self::IDENTIFIER_ITEMS);
    }

    /**
     * Resets the cache
     *
     * @return bool
     */
    public function resetCache()
    {
        return $this->clearForIdentifier(self::IDENTIFIER_ITEMS);
    }
}