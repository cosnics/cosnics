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
use Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Libraries\Rights\Storage\Repository\RightsRepository;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Libraries\Rights\Service\RightsService
{
    public const TYPE_ITEM = 1;
    public const VIEW_RIGHT = 1;

    private ConfigurationConsulter $configurationConsulter;

    private GroupEntityProvider $groupEntityProvider;

    private UserEntityProvider $userEntityProvider;

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

    public function areRightsEnabled(): bool
    {
        $setting = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Menu', 'enable_rights']);

        return $setting == 1;
    }

    /**
     * @throws \Exception
     */
    public function canUserViewItem(User $user, Item $item): bool
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
     * @throws \Exception
     */
    public function createItemRightsLocation(Item $item, bool $returnLocation = false)
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);

        return $this->createRightsLocationFromParameters(
            self::TYPE_ITEM, $item->getId(), 0, $parentLocation->getId(), 0, 0, self::TREE_TYPE_ROOT, $returnLocation
        );
    }

    /**
     * @throws \Exception
     */
    public function createItemRightsLocationWithViewRightForEveryone(Item $item): bool
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
     * @throws \Exception
     */
    public function createRoot(bool $returnLocation = true)
    {
        return $this->createSubtreeRootLocation(0, self::TREE_TYPE_ROOT, $returnLocation);
    }

    public function deleteItemRightsLocation(Item $item): bool
    {
        $rightsLocation = $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);

        return $this->deleteRightsLocation($rightsLocation);
    }

    public function deleteViewRightForRightsLocationForEveryone(RightsLocation $rightsLocation): bool
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            $rightsLocation, 0, 0, self::VIEW_RIGHT
        );
    }

    public function determineParentRightsLocationForItem(Item $item): ?\Chamilo\Libraries\Rights\Domain\RightsLocation
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

    public function findRightsLocationForItem(Item $item): ?\Chamilo\Libraries\Rights\Domain\RightsLocation
    {
        return $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);
    }

    public function findRightsLocationForItemIdentifier(int $itemIdentifier): ?\Chamilo\Libraries\Rights\Domain\RightsLocation
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
    public function getAvailableEntities(): array
    {
        $entities = [];

        $entities[UserEntityProvider::ENTITY_TYPE] = $this->getUserEntityProvider();
        $entities[GroupEntityProvider::ENTITY_TYPE] = $this->getGroupEntityProvider();

        return $entities;
    }

    /**
     * @return int[]
     */
    public function getAvailableRights(): array
    {
        return ['View' => self::VIEW_RIGHT];
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getGroupEntityProvider(): GroupEntityProvider
    {
        return $this->groupEntityProvider;
    }

    protected function getRightsLocationEntityRightInstance(): RightsLocationEntityRight
    {
        return new RightsLocationEntityRight();
    }

    protected function getRightsLocationInstance(): RightsLocation
    {
        return new RightsLocation();
    }

    /**
     * @return int[][][]
     * @throws \Exception
     */
    public function getTargetUsersAndGroupsForRightsLocationAndAvailableRights(RightsLocation $rightsLocation): array
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
     * @throws \Exception
     */
    public function moveItemRightsLocation(Item $item): bool
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);
        $itemLocation = $this->findRightsLocationByParameters($item->getId(), self::TYPE_ITEM);

        return $this->getRightsRepository()->moveRightsLocation($itemLocation, $parentLocation->getId());
    }

    /**
     * @param int $itemIdentifier
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int[][] $values
     *
     * @return bool
     * @throws \Exception
     */
    public function saveRightsConfigurationForItemIdentifierAndUserFromValues(
        int $itemIdentifier, User $user, array $values
    ): bool
    {
        $rightsLocation = $this->findRightsLocationForItemIdentifier($itemIdentifier);

        return $this->saveRightsConfigurationForRightsLocationAndUserFromValues($rightsLocation, $user, $values);
    }

    /**
     * @throws \Exception
     */
    public function setRightsLocationViewRightForEveryone(RightsLocation $rightsLocation): bool
    {
        return $this->setRightsLocationEntityRight(self::VIEW_RIGHT, 0, 0, $rightsLocation->getId());
    }
}