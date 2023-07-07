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
class RightsService
{
    public const TYPE_ITEM = 1;
    public const VIEW_RIGHT = 1;

    protected RightsRepository $rightsRepository;

    protected \Chamilo\Libraries\Rights\Service\RightsService $rightsService;

    protected Translator $translator;

    protected UserService $userService;

    private ConfigurationConsulter $configurationConsulter;

    private GroupEntityProvider $groupEntityProvider;

    private UserEntityProvider $userEntityProvider;

    public function __construct(
        \Chamilo\Libraries\Rights\Service\RightsService $rightsService, RightsRepository $rightsRepository,
        UserService $userService, Translator $translator, ConfigurationConsulter $configurationConsulter,
        UserEntityProvider $userEntityProvider, GroupEntityProvider $groupEntityProvider
    )
    {
        $this->rightsService = $rightsService;
        $this->rightsRepository = $rightsRepository;
        $this->userService = $userService;
        $this->translator = $translator;
        $this->configurationConsulter = $configurationConsulter;
        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
    }

    public function areRightsEnabled(): bool
    {
        $setting = $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Menu', 'enable_rights']);

        return $setting == 1;
    }

    public function canUserViewItem(User $user, Item $item): bool
    {
        if (!$item->isIdentified())
        {
            return true;
        }

        try
        {
            return $this->getRightsService()->doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
                RightsLocation::class, RightsLocationEntityRight::class, $user->getId(), self::VIEW_RIGHT,
                $this->getAvailableEntities(), $item->getId(), self::TYPE_ITEM
            );
        }
        catch (RightsLocationNotFoundException)
        {
            return false;
        }
    }

    public function createItemRightsLocation(Item $item): bool
    {
        return $this->getRightsService()->createRightsLocation($this->initializeItemRightsLocation($item));
    }

    public function createItemRightsLocationWithViewRightForEveryone(Item $item): bool
    {
        $rightsLocation = $this->initializeItemRightsLocation($item);

        if (!$this->getRightsService()->createRightsLocation($rightsLocation))
        {
            return false;
        }

        if (!$this->setRightsLocationViewRightForEveryone($rightsLocation))
        {
            return false;
        }

        return true;
    }

    public function createRoot(): bool
    {
        return $this->getRightsService()->createSubtreeRootLocation(
            RightsLocation::class, '0', \Chamilo\Libraries\Rights\Service\RightsService::TREE_TYPE_ROOT
        );
    }

    public function deleteItemRightsLocation(Item $item): bool
    {
        $rightsLocation = $this->getRightsService()->findRightsLocationByParameters(
            RightsLocation::class, $item->getId(), self::TYPE_ITEM
        );

        return $this->getRightsService()->deleteRightsLocation(RightsLocationEntityRight::class, $rightsLocation);
    }

    public function deleteViewRightForRightsLocationForEveryone(RightsLocation $rightsLocation): bool
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightsForLocationAndParameters(
            RightsLocationEntityRight::class, $rightsLocation, '0', 0, self::VIEW_RIGHT
        );
    }

    public function determineParentRightsLocationForItem(Item $item): ?\Chamilo\Libraries\Rights\Domain\RightsLocation
    {
        $parentIdentifier = $item->getParentId();

        if (!$parentIdentifier)
        {
            return $this->getRightsService()->getRootLocation(RightsLocation::class);
        }
        else
        {
            return $this->getRightsService()->findRightsLocationByParameters(
                RightsLocation::class, $parentIdentifier, self::TYPE_ITEM
            );
        }
    }

    public function findRightsLocationForItem(Item $item): ?RightsLocation
    {
        return $this->getRightsService()->findRightsLocationByParameters(
            RightsLocation::class, $item->getId(), self::TYPE_ITEM
        );
    }

    public function findRightsLocationForItemIdentifier(string $itemIdentifier): ?RightsLocation
    {
        if ($itemIdentifier != 0)
        {
            return $this->getRightsService()->findRightsLocationByParameters(
                RightsLocation::class, $itemIdentifier, self::TYPE_ITEM
            );
        }
        else
        {
            return $this->getRightsService()->getRootLocation(RightsLocation::class);
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

    public function getRightsRepository(): RightsRepository
    {
        return $this->rightsRepository;
    }

    public function getRightsService(): \Chamilo\Libraries\Rights\Service\RightsService
    {
        return $this->rightsService;
    }

    /**
     * @return int[][][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getTargetUsersAndGroupsForRightsLocationAndAvailableRights(RightsLocation $rightsLocation): array
    {
        return $this->getRightsService()->getTargetEntitiesForRightsAndLocation(
            RightsLocationEntityRight::class, $this->getAvailableRights(), $rightsLocation
        );
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    public function getUserEntityProvider(): UserEntityProvider
    {
        return $this->userEntityProvider;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function initializeItemRightsLocation(Item $item): \Chamilo\Libraries\Rights\Domain\RightsLocation
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);

        return $this->getRightsService()->initializeRightsLocationFromParameters(
            RightsLocation::class, self::TYPE_ITEM, $item->getId(), 0, $parentLocation->getId()
        );
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function isUserAllowedToAccessComponent(User $user): bool
    {
        if (!$user->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $rightsAreEnabled = $this->areRightsEnabled();

        if (!$rightsAreEnabled)
        {
            throw new NotAllowedException();
        }

        return true;
    }

    public function moveItemRightsLocation(Item $item): bool
    {
        $parentLocation = $this->determineParentRightsLocationForItem($item);
        $itemLocation = $this->getRightsService()->findRightsLocationByParameters(
            RightsLocation::class, $item->getId(), self::TYPE_ITEM
        );

        return $this->getRightsRepository()->moveRightsLocation($itemLocation, $parentLocation->getId());
    }

    /**
     * @param int[][] $values
     */
    public function saveRightsConfigurationForItemIdentifierAndUserFromValues(
        string $itemIdentifier, User $user, array $values
    ): bool
    {
        $rightsLocation = $this->findRightsLocationForItemIdentifier($itemIdentifier);

        return $this->getRightsService()->saveRightsConfigurationForRightsLocationAndUserFromValues(
            RightsLocationEntityRight::class, $rightsLocation, $user, $values
        );
    }

    public function setRightsLocationViewRightForEveryone(
        \Chamilo\Libraries\Rights\Domain\RightsLocation $rightsLocation
    ): bool
    {
        return $this->getRightsService()->setRightsLocationEntityRight(
            RightsLocationEntityRight::class, self::VIEW_RIGHT, '0', 0, $rightsLocation->getId()
        );
    }
}