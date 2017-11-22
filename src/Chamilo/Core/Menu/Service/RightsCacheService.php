<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Cache\Doctrine\Service\DoctrinePhpFileCacheService;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsCacheService extends DoctrinePhpFileCacheService implements UserBasedCacheInterface
{

    /**
     *
     * @var ItemService
     */
    private $itemService;

    /**
     *
     * @param ItemService $itemService
     */
    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     *
     * @return \Chamilo\Core\Menu\Service\ItemService
     */
    public function getItemService()
    {
        return $this->itemService;
    }

    /**
     *
     * @param \Chamilo\Core\Menu\Service\ItemService $itemService
     */
    public function setItemService($itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::warmUpForIdentifier()
     */
    public function warmUpForIdentifier($identifier)
    {
        $itemRights = array();
        $items = $this->getItemService()->getRootItems();

        $entities = array();
        $entities[] = new UserEntity();
        $entities[] = new PlatformGroupEntity();

        foreach ($items as $item)
        {
            if (Rights::getInstance()->is_allowed(
                Rights::VIEW_RIGHT,
                Manager::context(),
                null,
                $entities,
                $item->get_id(),
                Rights::TYPE_ITEM))
            {
                $itemRights[$item->get_id()] = true;
            }
        }

        return $this->getCacheProvider()->save($identifier, $itemRights);
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService::getCachePathNamespace()
     */
    public function getCachePathNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::getIdentifiers()
     */
    public function getIdentifiers()
    {
        return \Chamilo\Libraries\Storage\DataManager\DataManager::distinct(
            User::class_name(),
            new DataClassDistinctParameters(
                null,
                new DataClassProperties(array(new PropertyConditionVariable(User::class, User::PROPERTY_ID)))));
    }

    /**
     *
     * @param User $user
     * @return boolean[]
     */
    public function getForUser(User $user)
    {
        return $this->getForIdentifier($user->get_id());
    }
}