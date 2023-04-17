<?php
namespace Chamilo\Core\Menu\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocation;
use Chamilo\Core\Menu\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Cache\Interfaces\UserBasedCacheInterface;
use Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Libraries\Rights\Storage\Repository\RightsRepository;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Libraries\Rights\Service\RightsService implements UserBasedCacheInterface
{
    public const TYPE_ITEM = 1;

    public const VIEW_RIGHT = 1;

    /**
     * @var \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @var \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    private $groupEntityProvider;

    /**
     * @var \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    private $userEntityProvider;

    /**
     * @param \Chamilo\Libraries\Rights\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider $userEntityProvider
     * @param \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider $groupEntityProvider
     */
    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator,
        ConfigurationConsulter $configurationConsulter, UserEntityProvider $userEntityProvider,
        GroupEntityProvider $groupEntityProvider
    )
    {
        parent::__construct($rightsRepository, $userService, $translator);

        $this->configurationConsulter = $configurationConsulter;
        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
    }

    /**
     * @return bool
     */
    public function areRightsEnabled()
    {
        $setting = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Menu', 'enable_rights']);

        return $setting == 1;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     * @throws \Exception
     */
    public function canUserViewItem(User $user, Item $item)
    {
        if (!$item->isIdentified())
        {
            return true;
        }

        try
        {
            return $this->doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
                $user->getId(), self::VIEW_RIGHT, $this->getAvailableEntities(), $item->getId(), self::TYPE_ITEM
            );
        }
        catch (RightsLocationNotFoundException $exception)
        {
            return false;
        }
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     * @param bool $returnLocation
     *
     * @return bool
     * @throws \Exception
     */
    public function createItemRightsLocation(Item $item, bool $returnLocation = false)
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);

        return $this->createRightsLocationFromParameters(
            self::TYPE_ITEM, $item->getId(), false, $parentLocation->getId(), false, 0, self::TREE_TYPE_ROOT,
            $returnLocation
        );
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     * @throws \Exception
     */
    public function createItemRightsLocationWithViewRightForEveryone(Item $item)
    {
        $rightsLocation = $this->createItemRightsLocation($item, true);

        if (!$rightsLocation instanceof RightsLocation)
        {
            return false;
        }

        if (!$this->setRightsLocationViewRightForEveryone($rightsLocation))
        {
            return false;
        }

        return true;
    }

    /**
     * @param bool $returnLocation
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocation
     * @throws \Exception
     */
    public function createRoot(bool $returnLocation = true)
    {
        return $this->createSubtreeRootLocation(0, self::TREE_TYPE_ROOT, $returnLocation);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     */
    public function deleteItemRightsLocation(Item $item)
    {
        $rightsLocation = $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);

        return $this->deleteRightsLocation($rightsLocation);
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\RightsLocation $rightsLocation
     *
     * @return bool
     */
    public function deleteViewRightForRightsLocationForEveryone(RightsLocation $rightsLocation)
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $rightsLocation, 0, 0, self::VIEW_RIGHT
        );
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool|\Chamilo\Libraries\Rights\Domain\RightsLocation
     */
    public function determineParentRightsLocationForItem(Item $item)
    {
        $parentIdentifier = $item->getParentId();

        if (!$parentIdentifier)
        {
            return $this->getRootLocation();
        }
        else
        {
            return $this->findRightsLocationByParameters($parentIdentifier, self::TYPE_ITEM);
        }
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocation
     */
    public function findRightsLocationForItem(Item $item)
    {
        return $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);
    }

    /**
     * @param int $itemIdentifier
     *
     * @return \Chamilo\Core\Menu\Storage\DataClass\RightsLocation
     */
    public function findRightsLocationForItemIdentifier(int $itemIdentifier)
    {
        if ($itemIdentifier != 0)
        {
            return $this->findRightsLocationByParameters($itemIdentifier, self::TYPE_ITEM);
        }
        else
        {
            return $this->getRootLocation();
        }
    }

    /**
     * @return \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[]
     */
    public function getAvailableEntities()
    {
        $entities = [];

        $entities[UserEntityProvider::ENTITY_TYPE] = $this->getUserEntityProvider();
        $entities[GroupEntityProvider::ENTITY_TYPE] = $this->getGroupEntityProvider();

        return $entities;
    }

    /**
     * @return int
     */
    public function getAvailableRights()
    {
        return ['View' => self::VIEW_RIGHT];
    }

    /**
     * @return \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @return \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    public function getGroupEntityProvider(): GroupEntityProvider
    {
        return $this->groupEntityProvider;
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\RightsLocationEntityRight
     */
    protected function getRightsLocationEntityRightInstance()
    {
        return new RightsLocationEntityRight();
    }

    /**
     * @return \Chamilo\Core\Menu\Storage\DataClass\RightsLocation
     */
    protected function getRightsLocationInstance()
    {
        return new RightsLocation();
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\RightsLocation $rightsLocation
     *
     * @return int
     * @throws \Exception
     */
    public function getTargetUsersAndGroupsForRightsLocationAndAvailableRights(RightsLocation $rightsLocation)
    {
        return $this->getTargetEntitiesForRightsAndLocation($this->getAvailableRights(), $rightsLocation);
    }

    /**
     * @return \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    public function getUserEntityProvider(): UserEntityProvider
    {
        return $this->userEntityProvider;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function isUserAllowedToAccessComponent(User $user)
    {
        if (!$user->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $rightsAreEnabled = $this->areRightsEnabled();

        if (!$rightsAreEnabled)
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\Item $item
     *
     * @return bool
     */
    public function moveItemRightsLocation(Item $item)
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);
        $itemLocation = $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);

        return $this->getRightsRepository()->moveRightsLocation($itemLocation, $parentLocation->getId());
    }

    /**
     * @param int $itemIdentifier
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $values
     *
     * @return bool
     * @throws \Exception
     */
    public function saveRightsConfigurationForItemIdentifierAndUserFromValues(
        int $itemIdentifier, User $user, array $values
    )
    {
        $rightsLocation = $this->findRightsLocationForItemIdentifier($itemIdentifier);

        return $this->saveRightsConfigurationForRightsLocationAndUserFromValues($rightsLocation, $user, $values);
    }

    /**
     * @param \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @param \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider $groupEntityProvider
     */
    public function setGroupEntityProvider(GroupEntityProvider $groupEntityProvider): void
    {
        $this->groupEntityProvider = $groupEntityProvider;
    }

    /**
     * @param \Chamilo\Core\Menu\Storage\DataClass\RightsLocation $rightsLocation
     *
     * @return bool
     * @throws \Exception
     */
    public function setRightsLocationViewRightForEveryone(RightsLocation $rightsLocation)
    {
        return $this->setRightsLocationEntityRight(self::VIEW_RIGHT, 0, 0, $rightsLocation->getId());
    }

    /**
     * @param \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider $userEntityProvider
     */
    public function setUserEntityProvider(UserEntityProvider $userEntityProvider): void
    {
        $this->userEntityProvider = $userEntityProvider;
    }
}