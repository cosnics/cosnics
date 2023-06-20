<?php
namespace Chamilo\Core\Repository\Quota\Rights\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Repository\Quota\Rights\Form\RightsGroupForm;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Core\Repository\Quota\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Repository\Quota\Rights\Table\EntityTableRenderer;
use Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight as RightsLocationEntityRightAlias;
use Chamilo\Libraries\Rights\Form\RightsForm;
use Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService
{

    public const VIEW_RIGHT = 1;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected RightsRepository $rightsRepository;

    protected \Chamilo\Libraries\Rights\Service\RightsService $rightsService;

    protected Translator $translator;

    protected UserService $userService;

    private array $authorizedUsersCache = [];

    private ConfigurationConsulter $configurationConsulter;

    private GroupEntityProvider $groupEntityProvider;

    private GroupService $groupService;

    private StorageSpaceCalculator $storageSpaceCalculator;

    /**
     * @var string[]
     */
    private array $targetUsersCache = [];

    private UserEntityProvider $userEntityProvider;

    public function __construct(
        \Chamilo\Libraries\Rights\Service\RightsService $rightsService, RightsRepository $rightsRepository,
        UserService $userService, Translator $translator, UserEntityProvider $userEntityProvider,
        GroupEntityProvider $groupEntityProvider, GroupService $groupService,
        StorageSpaceCalculator $storageSpaceCalculator, ConfigurationConsulter $configurationConsulter,
        GroupsTreeTraverser $groupsTreeTraverser
    )
    {
        $this->rightsService = $rightsService;
        $this->rightsRepository = $rightsRepository;
        $this->userService = $userService;
        $this->translator = $translator;
        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
        $this->groupService = $groupService;
        $this->storageSpaceCalculator = $storageSpaceCalculator;
        $this->configurationConsulter = $configurationConsulter;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     */
    public function canUserRequestAdditionalStorageSpace(User $user): bool
    {
        $quotaCalculator = $this->getStorageSpaceCalculator();

        if (!$quotaCalculator->isStorageQuotumEnabled())
        {
            return false;
        }

        $configurationConsulter = $this->getConfigurationConsulter();

        $quotaStep = (int) $configurationConsulter->getSetting(['Chamilo\Core\Repository', 'step']);
        $allowRequest = $configurationConsulter->getSetting(['Chamilo\Core\Repository', 'allow_request']);

        if (!$quotaCalculator->isQuotumDefinedForUser($user))
        {
            return false;
        }

        if ($this->canUserViewQuotaRequests($user) &&
            $quotaCalculator->getAvailableAllocatedStorageSpace() > $quotaStep)
        {
            return true;
        }

        if ($allowRequest)
        {
            if ($quotaCalculator->getAvailableAllocatedStorageSpace() > $quotaStep)
            {
                return true;
            }
        }

        return false;
    }

    public function canUserSetRightsForQuotaRequests(User $user): bool
    {
        return $user->isPlatformAdmin();
    }

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     */
    public function canUserUpgradeStorageSpace(User $user): bool
    {
        $quotaCalculator = $this->getStorageSpaceCalculator();
        $configurationConsulter = $this->getConfigurationConsulter();

        $quotaStep = (int) $configurationConsulter->getSetting(['Chamilo\Core\Repository', 'step']);
        $allowUpgrade = (boolean) $configurationConsulter->getSetting(['Chamilo\Core\Repository', 'allow_upgrade']);
        $maximumUserDiskSpace = (int) $configurationConsulter->getSetting(['Chamilo\Core\Repository', 'maximum_user']);

        if (!$quotaCalculator->isQuotumDefinedForUser($user))
        {
            return false;
        }

        $availableAllocatedStorageSpace = $quotaCalculator->getAvailableAllocatedStorageSpace();

        if ($this->canUserViewQuotaRequests($user) && $availableAllocatedStorageSpace > $quotaStep)
        {
            return true;
        }

        if ($allowUpgrade)
        {
            if ($maximumUserDiskSpace == 0 && $availableAllocatedStorageSpace > $quotaStep)
            {
                return true;
            }
            elseif ($user->get_disk_quota() < $maximumUserDiskSpace && $availableAllocatedStorageSpace > $quotaStep)
            {
                return true;
            }
        }

        return false;
    }

    public function canUserViewAllQuotaRequests(User $user): bool
    {
        return $user->isPlatformAdmin();
    }

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     */
    public function canUserViewQuotaRequests(User $user): bool
    {
        return $this->getRightsService()->doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
            RightsLocation::class, RightsLocationEntityRight::class, $user->getId(), self::VIEW_RIGHT,
            $this->getAvailableEntities()
        );
    }

    public function countAllRightsLocationEntityRightGroups(): int
    {
        return $this->getRightsRepository()->countRightsLocationEntityRightGroups();
    }

    protected function createRightsLocationEntityRightGroupFromParameters(
        string $rightsLocationEntityRightIdentifier, string $groupIdentifier
    ): bool
    {
        $rightsLocationEntityRightGroup = new RightsLocationEntityRightGroup();

        $rightsLocationEntityRightGroup->set_location_entity_right_id($rightsLocationEntityRightIdentifier);
        $rightsLocationEntityRightGroup->set_group_id($groupIdentifier);

        return $this->getRightsRepository()->createRightsLocationEntityRightGroup($rightsLocationEntityRightGroup);
    }

    public function createRoot(): bool
    {
        return $this->getRightsService()->createSubtreeRootLocation(
            RightsLocation::class, '0', \Chamilo\Libraries\Rights\Service\RightsService::TREE_TYPE_ROOT
        );
    }

    public function deleteRightLocationEntityRightGroupsForRightsLocationEntityRight(
        RightsLocationEntityRightAlias $rightsLocationEntityRight
    ): bool
    {
        return $this->getRightsRepository()->deleteRightLocationEntityRightGroupsForRightsLocationEntityRight(
            $rightsLocationEntityRight
        );
    }

    protected function deleteRightsLocationEntityRight(
        RightsLocationEntityRightAlias $rightsLocationEntityRight
    ): bool
    {
        if (!$this->deleteRightLocationEntityRightGroupsForRightsLocationEntityRight($rightsLocationEntityRight))
        {
            return false;
        }

        return $this->getRightsService()->deleteRightsLocationEntityRight(
            $rightsLocationEntityRight
        );
    }

    public function deleteRightsLocationEntityRightGroup(RightsLocationEntityRightGroup $rightsLocationEntityRightGroup
    ): bool
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightGroup($rightsLocationEntityRightGroup);
    }

    protected function findRightsLocationEntityRightForEntityIdentifierAndType(string $entityIdentifier, int $entityType
    ): ?RightsLocationEntityRightAlias
    {
        return $this->getRightsService()->findRightsLocationEntityRightByParameters(
            RightsLocationEntityRight::class, self::VIEW_RIGHT, $entityIdentifier, $entityType,
            $this->getRightsService()->getRootLocationIdentifier(RightsLocation::class)
        );
    }

    /**
     * @param string[] $identifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupByIdentifiers(array $identifiers = []): ArrayCollection
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightGroupByIdentifiers($identifiers);
    }

    protected function findRightsLocationEntityRightGroupByParameters(
        string $rightsLocationEntityRightIdentifier, string $groupIdentifier
    ): ?RightsLocationEntityRightGroup
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightGroupByParameters(
            $rightsLocationEntityRightIdentifier, $groupIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupIdentifiersForUser(User $user): array
    {
        $rightsLocationEntityRightGroups = $this->findRightsLocationEntityRightGroupsForUser($user);
        $groupIdentifiers = [];

        foreach ($rightsLocationEntityRightGroups as $rightsLocationEntityRightGroup)
        {
            $groupIdentifiers[] = $rightsLocationEntityRightGroup->get_group_id();
        }

        return $groupIdentifiers;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight> $locationEntityRights
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupsForRightsLocationEntityRights(
        ArrayCollection $locationEntityRights
    ): ArrayCollection
    {
        $locationEntityRightIdentifiers = [];

        foreach ($locationEntityRights as $locationEntityRight)
        {
            $locationEntityRightIdentifiers[] = $locationEntityRight->getId();
        }

        return $this->getRightsRepository()->findRightsLocationEntityRightGroupsForRightsLocationEntityRightIdentifiers(
            $locationEntityRightIdentifiers
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupsForSubscribedUserGroups(User $user): ArrayCollection
    {
        $userGroupIdentifiers =
            $this->getGroupsTreeTraverser()->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        $locationEntityRights = $this->findRightsLocationEntityRightsForEntityIdentifiersAndType(
            $userGroupIdentifiers, GroupEntityProvider::ENTITY_TYPE
        );

        return $this->findRightsLocationEntityRightGroupsForRightsLocationEntityRights($locationEntityRights);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupsForUser(User $user): ArrayCollection
    {
        $locationEntityRight = $this->findRightsLocationEntityRightForEntityIdentifierAndType(
            $user->getId(), UserEntityProvider::ENTITY_TYPE
        );

        if ($locationEntityRight instanceof RightsLocationEntityRight)
        {

            return $this->getRightsRepository()->findRightsLocationEntityRightGroupsForRightsLocationEntityRight(
                $locationEntityRight
            );
        }
        else
        {
            return new ArrayCollection([]);
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightGroupsIdentifiersForUser(User $user): array
    {
        $rightsLocationEntityRightGroups = $this->findRightsLocationEntityRightGroupsForSubscribedUserGroups($user);
        $groupIdentifiers = [];

        foreach ($rightsLocationEntityRightGroups as $rightsLocationEntityRightGroup)
        {
            $groupIdentifiers[] = $rightsLocationEntityRightGroup->get_group_id();
        }

        return $groupIdentifiers;
    }

    /**
     * @param string[] $entityIdentifiers
     * @param int $entityType
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findRightsLocationEntityRightsForEntityIdentifiersAndType(
        array $entityIdentifiers, int $entityType
    ): ArrayCollection
    {
        return $this->getRightsService()->findRightsLocationEntityRightsByParameters(
            RightsLocationEntityRight::class, self::VIEW_RIGHT, $entityIdentifiers, $entityType,
            $this->getRightsService()->getRootLocationIdentifier(RightsLocation::class)
        );
    }

    /**
     * @param string[] $userGroupIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findRightsLocationEntityRightsForTargetGroupIdentifiers(array $userGroupIdentifiers
    ): ArrayCollection
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightsForTargetGroupIdentifiers(
            $userGroupIdentifiers
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAuthorizedUserIdentifiersForUser(User $user): array
    {
        $userGroupIdentifiers =
            $this->getGroupsTreeTraverser()->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        $rightsLocationEntityRights =
            $this->findRightsLocationEntityRightsForTargetGroupIdentifiers($userGroupIdentifiers);

        $userIdentifiers = [];

        foreach ($rightsLocationEntityRights as $rightsLocationEntityRight)
        {
            switch ($rightsLocationEntityRight->get_entity_type())
            {
                case UserEntityProvider::ENTITY_TYPE:
                    $userIdentifiers[] = $rightsLocationEntityRight->get_entity_id();
                    break;
                case GroupEntityProvider::ENTITY_TYPE:
                    $group =
                        $this->getGroupService()->findGroupByIdentifier($rightsLocationEntityRight->get_entity_id());

                    $groupUsers = $this->getGroupsTreeTraverser()->findUserIdentifiersForGroup($group, true, true);

                    $userIdentifiers = array_merge($userIdentifiers, $groupUsers);
                    break;
            }
        }

        return array_unique($userIdentifiers);
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAuthorizedUsersForUser(User $user): array
    {
        if (!isset($this->authorizedUsersCache[$user->getId()]))
        {
            $userIdentifiers = $this->getAuthorizedUserIdentifiersForUser($user);

            if (count($userIdentifiers) > 0)
            {
                $users = $this->getUserService()->findUsersByIdentifiers($userIdentifiers);
            }
            else
            {
                $users = $this->getUserService()->findPlatformAdministrators();
            }

            $this->authorizedUsersCache[$user->getId()] = $users;
        }

        return $this->authorizedUsersCache[$user->getId()];
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

    public function getAvailableEntityByType(int $entityType): RightsEntityProvider
    {
        $entities = $this->getAvailableEntities();

        return $entities[$entityType];
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

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    /**
     * @return string[][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getRightsLocationEntityRightGroupsWithEntityAndGroup(
        ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): array
    {
        $groupRecordCollection =
            $this->getRightsRepository()->findRightsLocationEntityRightGroupsWithEntityAndGroupRecords();

        foreach ($groupRecordCollection as &$groupRecord)
        {
            $entityType = $groupRecord[RightsLocationEntityRightAlias::PROPERTY_ENTITY_TYPE];
            $entityIdentifier = $groupRecord[RightsLocationEntityRightAlias::PROPERTY_ENTITY_ID];

            if ($entityType != 0)
            {
                $entityService = $this->getAvailableEntityByType($entityType);

                $groupRecord[EntityTableRenderer::PROPERTY_ENTITY_TITLE] =
                    $entityService->getEntityTitleByIdentifier($entityIdentifier);
                $groupRecord[EntityTableRenderer::PROPERTY_ENTITY_DESCRIPTION] =
                    $entityService->getEntityDescriptionByIdentifier($entityIdentifier);
                $groupRecord[EntityTableRenderer::PROPERTY_ENTITY_GLYPH] = $entityService->getEntityGlyph();
            }
            else
            {
                $groupRecord[EntityTableRenderer::PROPERTY_ENTITY_TITLE] = $this->getTranslator()->trans('Everyone');
                $groupRecord[EntityTableRenderer::PROPERTY_ENTITY_DESCRIPTION] = '';
                $groupRecord[EntityTableRenderer::PROPERTY_ENTITY_GLYPH] = new FontAwesomeGlyph('globe');
            }

            $group = $this->getGroupService()->findGroupByIdentifier(
                $groupRecord[RightsRepository::PROPERTY_GROUP_ID]
            );

            $groupRecord[EntityTableRenderer::PROPERTY_GROUP_NAME] = $group->get_name();
            $groupRecord[EntityTableRenderer::PROPERTY_GROUP_PATH] =
                $this->getGroupsTreeTraverser()->getGroupPath($group);
        }

        $orderProperty = $orderBy->getFirst();
        $orderPropertyValue = $orderProperty->getConditionVariable()->getValue();
        $orderDirection = $orderProperty->getDirection();

        $groupRecords = $groupRecordCollection->toArray();

        uasort(
            $groupRecords, function ($groupRecordOne, $groupRecordTwo) use ($orderPropertyValue, $orderDirection) {

            if ($orderDirection == SORT_DESC)
            {
                return strcmp(
                    $groupRecordTwo[$orderPropertyValue], $groupRecordOne[$orderPropertyValue]
                );
            }
            else
            {
                return strcmp(
                    $groupRecordOne[$orderPropertyValue], $groupRecordTwo[$orderPropertyValue]
                );
            }
        }
        );

        return array_slice($groupRecords, $offset, $count);
    }

    protected function getRightsLocationEntityRightInstance(): RightsLocationEntityRightAlias
    {
        return new RightsLocationEntityRight();
    }

    protected function getRightsLocationInstance(): \Chamilo\Libraries\Rights\Domain\RightsLocation
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

    public function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->storageSpaceCalculator;
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getTargetGroupIdentifiersForUser(User $user): array
    {
        $userTargetGroupIdentifiers = $this->findRightsLocationEntityRightGroupIdentifiersForUser($user);
        $userGroupTargetGroupIdentifiers = $this->findRightsLocationEntityRightGroupsIdentifiersForUser($user);

        return array_unique(array_merge($userTargetGroupIdentifiers, $userGroupTargetGroupIdentifiers));
    }

    /**
     * @return string[][][]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getTargetUsersAndGroupsForAvailableRights(): array
    {
        return $this->getRightsService()->getTargetEntitiesForRightsAndLocation(
            RightsLocationEntityRight::class, $this->getAvailableRights(),
            $this->getRightsService()->getRootLocation(RightsLocation::class)
        );
    }

    /**
     * @return string[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getTargetUsersForUser(User $user): array
    {
        if (!isset($this->targetUsersCache[$user->getId()]))
        {
            $targetGroupIdentifiers = $this->getTargetGroupIdentifiersForUser($user);
            $targetGroups = $this->getGroupService()->findGroupsByIdentifiers($targetGroupIdentifiers);

            $targetUserIdentifiers = [];

            foreach ($targetGroups as $targetGroup)
            {
                $userIdentifiers =
                    $this->getGroupsTreeTraverser()->findUserIdentifiersForGroup($targetGroup, true, true);
                $targetUserIdentifiers = array_merge($targetUserIdentifiers, $userIdentifiers);
            }

            $this->targetUsersCache[$user->getId()] = array_unique($targetUserIdentifiers);
        }

        return $this->targetUsersCache[$user->getId()];
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserEntityProvider(): UserEntityProvider
    {
        return $this->userEntityProvider;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function isUserIdentifierTargetForUser(string $userIdentifier, User $user): bool
    {
        return $user->isPlatformAdmin() || in_array($userIdentifier, $this->getTargetUsersForUser($user));
    }

    /**
     * @param int[][] $values
     */
    public function saveRightsConfigurationForUserFromValues(User $user, array $values): bool
    {
        return $this->getRightsService()->saveRightsConfigurationForRightsLocationAndUserFromValues(
            RightsLocationEntityRight::class, $this->getRightsService()->getRootLocation(RightsLocation::class), $user,
            $values
        );
    }

    /**
     * @param int[][] $values
     */
    public function setRightsConfigurationForUserFromValues(User $user, array $values): bool
    {
        $rightsLocation = $this->getRightsService()->getRootLocation(RightsLocation::class);

        if (!$this->getRightsService()->setRightsLocationEntityRightsForRightsLocationAndUserFromValues(
            RightsLocationEntityRight::class, $rightsLocation, $user, $values
        ))
        {
            return false;
        }

        if (!$this->setRightsLocationEntityRightGroupsForRightsLocationFromValues($rightsLocation, $user, $values))
        {
            return false;
        }

        return true;
    }

    /**
     * @param int[][] $values
     */
    protected function setRightsLocationEntityRightGroupsForRightsLocationEntityRightFromValues(
        RightsLocationEntityRightAlias $rightsLocationEntityRight, array $values
    ): bool
    {
        if (!array_key_exists(RightsGroupForm::PROPERTY_TARGET_GROUPS, $values))
        {
            return true;
        }

        foreach ($values[RightsGroupForm::PROPERTY_TARGET_GROUPS][GroupEntityProvider::ENTITY_TYPE] as $groupIdentifier)
        {
            $rightsLocationEntityRightGroup = $this->findRightsLocationEntityRightGroupByParameters(
                $rightsLocationEntityRight->getId(), $groupIdentifier
            );

            if (!$rightsLocationEntityRightGroup instanceof RightsLocationEntityRightGroup)
            {
                if (!$this->createRightsLocationEntityRightGroupFromParameters(
                    $rightsLocationEntityRight->getId(), $groupIdentifier
                ))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param int[][] $values
     */
    protected function setRightsLocationEntityRightGroupsForRightsLocationFromValues(
        \Chamilo\Libraries\Rights\Domain\RightsLocation $location, User $user, array $values
    ): bool
    {
        $success = true;

        foreach ($values[RightsForm::PROPERTY_RIGHT_OPTION] as $rightIdentifier => $rightsOption)
        {
            switch ($rightsOption)
            {
                case RightsForm::RIGHT_OPTION_ALL :
                    $locationEntityRight = $this->getRightsService()->findRightsLocationEntityRightByParameters(
                        RightsLocationEntityRight::class, $rightIdentifier, '0', 0, $location->getId()
                    );

                    $success &= $this->setRightsLocationEntityRightGroupsForRightsLocationEntityRightFromValues(
                        $locationEntityRight, $values
                    );
                    break;
                case RightsForm::RIGHT_OPTION_ME :
                    $locationEntityRight = $this->getRightsService()->findRightsLocationEntityRightByParameters(
                        RightsLocationEntityRight::class, $rightIdentifier, $user->getId(),
                        UserEntityProvider::ENTITY_TYPE, $location->getId()
                    );

                    $success &= $this->setRightsLocationEntityRightGroupsForRightsLocationEntityRightFromValues(
                        $locationEntityRight, $values
                    );
                    break;
                case RightsForm::RIGHT_OPTION_SELECT :

                    foreach (
                        $values[RightsForm::PROPERTY_TARGETS][$rightIdentifier] as $entityType => $entityIdentifiers
                    )
                    {
                        foreach ($entityIdentifiers as $entityIdentifier)
                        {
                            $locationEntityRight = $this->getRightsService()->findRightsLocationEntityRightByParameters(
                                RightsLocationEntityRight::class, $rightIdentifier, $entityIdentifier, $entityType,
                                $location->getId()
                            );

                            $success &= $this->setRightsLocationEntityRightGroupsForRightsLocationEntityRightFromValues(
                                $locationEntityRight, $values
                            );
                        }
                    }
            }
        }

        return $success;
    }
}