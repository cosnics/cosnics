<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Doctrine\Provider\PhpFileCache;
use Chamilo\Libraries\File\Path;

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
     */
    public function getItemsByParentIdentifier($parentIdentifier)
    {
        return $this->getItemRepository()->findItemsByParentIdentifier($parentIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item[] $items
     * @param User $user
     * @return boolean[]
     */
    public function determineRightsForItemsAndUser($items, User $user)
    {
        $cache = new PhpFileCache(Path :: getInstance()->getCachePath(__NAMESPACE__));
        $cacheIdentifier = md5(serialize(array(__METHOD__, $user->get_id())));

        if (! $cache->contains($cacheIdentifier))
        {
            $itemRights = array();

            $entities = array();
            $entities[] = new UserEntity();
            $entities[] = new PlatformGroupEntity();

            foreach ($items as $item)
            {
                if (Rights :: get_instance()->is_allowed(
                    Rights :: VIEW_RIGHT,
                    Manager :: context(),
                    null,
                    $entities,
                    $item->get_id(),
                    Rights :: TYPE_ITEM))
                {
                    $itemRights[$item->get_id()] = true;
                }
            }

            $cache->save($cacheIdentifier, $itemRights);
        }

        return $cache->fetch($cacheIdentifier);
    }
}