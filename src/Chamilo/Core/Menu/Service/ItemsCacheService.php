<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrineFilesystemCacheService;

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
     * @param \Chamilo\Core\Menu\Service\ItemRepository $itemRepository
     */
    public function setItemRepository($itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $itemsByParentIdentifier = $this->processItemResultSet($this->getItemRepository()->findItems());
        return $this->getCacheProvider()->save($identifier, $itemsByParentIdentifier);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\ResultSet\ResultSet $itemResultSet
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    private function processItemResultSet(\Chamilo\Libraries\Storage\ResultSet\ResultSet $itemResultSet)
    {
        while ($item = $itemResultSet->next_result())
        {
            $item->get_additional_properties();
            $item->get_titles();

            if (! isset($itemsByParentIdentifier[$item->get_parent()]))
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
        return array(self :: IDENTIFIER_ITEMS);
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\Item[]
     */
    public function getItems()
    {
        return $this->getForIdentifier(self :: IDENTIFIER_ITEMS);
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