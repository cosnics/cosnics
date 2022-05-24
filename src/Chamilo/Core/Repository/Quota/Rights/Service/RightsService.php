<?php
namespace Chamilo\Core\Repository\Quota\Rights\Service;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Quota\Rights\Form\RightsGroupForm;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Core\Repository\Quota\Rights\Storage\Repository\RightsRepository as QuotaRightsRepository;
use Chamilo\Core\Repository\Quota\Rights\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Rights\Form\RightsForm;
use Chamilo\Libraries\Rights\Storage\Repository\RightsRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassCollection;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Libraries\Rights\Service\RightsService
{

    const VIEW_RIGHT = 1;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User[]
     */
    private $authorizedUsersCache = [];

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    private $configurationConsulter;

    /**
     * @var \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    private $groupEntityProvider;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;

    /**
     * @var \Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator
     */
    private $storageSpaceCalculator;

    /**
     * @var integer[]
     */
    private $targetUsersCache = [];

    /**
     * @var \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    private $userEntityProvider;

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider $userEntityProvider
     * @param \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider $groupEntityProvider
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator $storageSpaceCalculator
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator,
        UserEntityProvider $userEntityProvider, GroupEntityProvider $groupEntityProvider, GroupService $groupService,
        StorageSpaceCalculator $storageSpaceCalculator, ConfigurationConsulter $configurationConsulter
    )
    {
        parent::__construct($rightsRepository, $userService, $translator);

        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
        $this->groupService = $groupService;
        $this->storageSpaceCalculator = $storageSpaceCalculator;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \Exception
     */
    public function canUserRequestAdditionalStorageSpace(User $user)
    {
        $quotaCalculator = $this->getStorageSpaceCalculator();

        if (!$quotaCalculator->isStorageQuotumEnabled())
        {
            return false;
        }

        $configurationConsulter = $this->getConfigurationConsulter();

        $quotaStep = (int) $configurationConsulter->getSetting(array('Chamilo\Core\Repository', 'step'));
        $allowRequest = $configurationConsulter->getSetting(array('Chamilo\Core\Repository', 'allow_request'));

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

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function canUserSetRightsForQuotaRequests(User $user)
    {
        return $user->is_platform_admin();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \Exception
     * @see Calculator::upgradeAllowed()
     */
    public function canUserUpgradeStorageSpace(User $user)
    {
        $quotaCalculator = $this->getStorageSpaceCalculator();
        $configurationConsulter = $this->getConfigurationConsulter();

        $quotaStep = (int) $configurationConsulter->getSetting(array('Chamilo\Core\Repository', 'step'));
        $allowUpgrade =
            (boolean) $configurationConsulter->getSetting(array('Chamilo\Core\Repository', 'allow_upgrade'));
        $maximumUserDiskSpace =
            (int) $configurationConsulter->getSetting(array('Chamilo\Core\Repository', 'maximum_user'));

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

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function canUserViewAllQuotaRequests(User $user)
    {
        return $user->is_platform_admin();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     */
    public function canUserViewQuotaRequests(User $user)
    {
        return $this->doesUserIdentifierHaveRightForEntitiesAndLocationIdentifier(
            $user->getId(), self::VIEW_RIGHT, $this->getAvailableEntities()
        );
    }

    /**
     * @return integer
     */
    public function countAllRightsLocationEntityRightGroups()
    {
        return $this->getRightsRepository()->countRightsLocationEntityRightGroups();
    }

    /**
     * @param integer $rightsLocationEntityRightIdentifier
     * @param integer $groupIdentifier
     *
     * @return boolean
     * @throws \Exception
     */
    protected function createRightsLocationEntityRightGroupFromParameters(
        int $rightsLocationEntityRightIdentifier, int $groupIdentifier
    )
    {
        $rightsLocationEntityRightGroup = new RightsLocationEntityRightGroup();

        $rightsLocationEntityRightGroup->set_location_entity_right_id($rightsLocationEntityRightIdentifier);
        $rightsLocationEntityRightGroup->set_group_id($groupIdentifier);

        return $this->getRightsRepository()->createRightsLocationEntityRightGroup($rightsLocationEntityRightGroup);
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
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return boolean
     */
    public function deleteRightLocationEntityRightGroupsForRightsLocationEntityRight(
        RightsLocationEntityRight $rightsLocationEntityRight
    )
    {
        return $this->getRightsRepository()->deleteRightLocationEntityRightGroupsForRightsLocationEntityRight(
            $rightsLocationEntityRight
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight $rightsLocationEntityRight
     *
     * @return boolean
     */
    protected function deleteRightsLocationEntityRight(
        \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight $rightsLocationEntityRight
    )
    {
        if (!$this->deleteRightLocationEntityRightGroupsForRightsLocationEntityRight($rightsLocationEntityRight))
        {
            return false;
        }

        return parent::deleteRightsLocationEntityRight(
            $rightsLocationEntityRight
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup $rightsLocationEntityRightGroup
     *
     * @return boolean
     */
    public function deleteRightsLocationEntityRightGroup(RightsLocationEntityRightGroup $rightsLocationEntityRightGroup)
    {
        return $this->getRightsRepository()->deleteRightsLocationEntityRightGroup($rightsLocationEntityRightGroup);
    }

    /**
     * @param integer $entityIdentifier
     * @param integer $entityType
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight
     */
    protected function findRightsLocationEntityRightForEntityIdentifierAndType(int $entityIdentifier, int $entityType)
    {
        return $this->findRightsLocationEntityRightByParameters(
            self::VIEW_RIGHT, $entityIdentifier, $entityType, $this->getRootLocationIdentifier()
        );
    }

    /**
     * @param integer[] $identifiers
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     */
    public function findRightsLocationEntityRightGroupByIdentifiers(array $identifiers = [])
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightGroupByIdentifiers($identifiers);
    }

    /**
     * @param integer $rightsLocationEntityRightIdentifier
     * @param integer $groupIdentifier
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup
     */
    protected function findRightsLocationEntityRightGroupByParameters(
        int $rightsLocationEntityRightIdentifier, int $groupIdentifier
    )
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightGroupByParameters(
            $rightsLocationEntityRightIdentifier, $groupIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer[]
     */
    public function findRightsLocationEntityRightGroupIdentifiersForUser(User $user)
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
     * @param \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight[] $locationEntityRights
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     */
    public function findRightsLocationEntityRightGroupsForRightsLocationEntityRights(
        DataClassCollection $locationEntityRights
    )
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
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     * @throws \Exception
     */
    public function findRightsLocationEntityRightGroupsForSubscribedUserGroups(User $user)
    {
        $userGroupIdentifiers =
            $this->getGroupService()->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

        $locationEntityRights = $this->findRightsLocationEntityRightsForEntityIdentifiersAndType(
            $userGroupIdentifiers, GroupEntityProvider::ENTITY_TYPE
        );

        return $this->findRightsLocationEntityRightGroupsForRightsLocationEntityRights($locationEntityRights);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     */
    public function findRightsLocationEntityRightGroupsForUser(User $user)
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
            return new DataClassCollection(RightsLocationEntityRight::class, []);
        }
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer[]
     * @throws \Exception
     */
    public function findRightsLocationEntityRightGroupsIdentifiersForUser(User $user)
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
     * @param integer[] $entityIdentifiers
     * @param integer $entityType
     *
     * @return \Chamilo\Libraries\Rights\Domain\RightsLocationEntityRight[]
     */
    protected function findRightsLocationEntityRightsForEntityIdentifiersAndType(
        array $entityIdentifiers, int $entityType
    )
    {
        return $this->findRightsLocationEntityRightsByParameters(
            self::VIEW_RIGHT, $entityIdentifiers, $entityType, $this->getRootLocationIdentifier()
        );
    }

    /**
     * @param integer[] $userGroupIdentifiers
     *
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight[]
     */
    public function findRightsLocationEntityRightsForTargetGroupIdentifiers(array $userGroupIdentifiers)
    {
        return $this->getRightsRepository()->findRightsLocationEntityRightsForTargetGroupIdentifiers(
            $userGroupIdentifiers
        );
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer[]
     * @throws \Exception
     */
    public function getAuthorizedUserIdentifiersForUser(User $user)
    {
        $userGroupIdentifiers =
            $this->getGroupService()->findAllSubscribedGroupIdentifiersForUserIdentifier($user->getId());

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
                    $userIdentifiers = array_merge($userIdentifiers, $group->get_users(true, true));
                    break;
            }
        }

        return array_unique($userIdentifiers);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     * @throws \Exception
     */
    public function getAuthorizedUsersForUser(User $user)
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
    public function getAvailableEntities()
    {
        $entities = [];

        $entities[UserEntityProvider::ENTITY_TYPE] = $this->getUserEntityProvider();
        $entities[GroupEntityProvider::ENTITY_TYPE] = $this->getGroupEntityProvider();

        return $entities;
    }

    /**
     * @param integer $entityType
     *
     * @return \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider
     */
    public function getAvailableEntityByType(int $entityType)
    {
        $entities = $this->getAvailableEntities();

        return $entities[$entityType];
    }

    /**
     * @return integer[]
     */
    public function getAvailableRights()
    {
        return array('View' => self::VIEW_RIGHT);
    }

    /**
     * @return \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function setConfigurationConsulter(ConfigurationConsulter $configurationConsulter): void
    {
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * @return \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    public function getGroupEntityProvider(): GroupEntityProvider
    {
        return $this->groupEntityProvider;
    }

    /**
     * @param \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider $groupEntityProvider
     */
    public function setGroupEntityProvider(GroupEntityProvider $groupEntityProvider): void
    {
        $this->groupEntityProvider = $groupEntityProvider;
    }

    /**
     * @return \Chamilo\Core\Group\Service\GroupService
     */
    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    /**
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function setGroupService(GroupService $groupService): void
    {
        $this->groupService = $groupService;
    }

    /**
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return string[][]
     * @throws \Exception
     */
    public function getRightsLocationEntityRightGroupsWithEntityAndGroup(
        int $offset = null, int $count = null, ?OrderBy $orderBy = null
    )
    {
        $groupRecordCollection =
            $this->getRightsRepository()->findRightsLocationEntityRightGroupsWithEntityAndGroupRecords();

        foreach ($groupRecordCollection as &$groupRecord)
        {
            $entityType = $groupRecord[RightsLocationEntityRight::PROPERTY_ENTITY_TYPE];
            $entityIdentifier = $groupRecord[RightsLocationEntityRight::PROPERTY_ENTITY_ID];

            if ($entityType != 0)
            {
                $entityService = $this->getAvailableEntityByType($entityType);

                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_TITLE] =
                    $entityService->getEntityTitleByIdentifier($entityIdentifier);
                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_DESCRIPTION] =
                    $entityService->getEntityDescriptionByIdentifier($entityIdentifier);
                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_GLYPH] = $entityService->getEntityGlyph();
            }
            else
            {
                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_TITLE] = $this->getTranslator()->trans('Everyone');
                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_DESCRIPTION] = '';
                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_GLYPH] = new FontAwesomeGlyph('globe');
            }

            $group =
                $this->getGroupService()->findGroupByIdentifier($groupRecord[QuotaRightsRepository::PROPERTY_GROUP_ID]);

            $groupRecord[EntityTableColumnModel::PROPERTY_GROUP_NAME] = $group->get_name();
            $groupRecord[EntityTableColumnModel::PROPERTY_GROUP_PATH] = $this->getGroupService()->getGroupPath($group);
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

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight
     */
    protected function getRightsLocationEntityRightInstance()
    {
        return new RightsLocationEntityRight();
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation
     */
    protected function getRightsLocationInstance()
    {
        return new RightsLocation();
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\Repository\RightsRepository
     */
    protected function getRightsRepository(): RightsRepository
    {
        return parent::getRightsRepository();
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator
     */
    public function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->storageSpaceCalculator;
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator $storageSpaceCalculator
     */
    public function setStorageSpaceCalculator(StorageSpaceCalculator $storageSpaceCalculator): void
    {
        $this->storageSpaceCalculator = $storageSpaceCalculator;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer[]
     * @throws \Exception
     */
    public function getTargetGroupIdentifiersForUser(User $user)
    {
        $userTargetGroupIdentifiers = $this->findRightsLocationEntityRightGroupIdentifiersForUser($user);
        $userGroupTargetGroupIdentifiers = $this->findRightsLocationEntityRightGroupsIdentifiersForUser($user);

        return array_unique(array_merge($userTargetGroupIdentifiers, $userGroupTargetGroupIdentifiers));
    }

    /**
     *
     * @return integer[][][]
     * @throws \Exception
     */
    public function getTargetUsersAndGroupsForAvailableRights()
    {
        return $this->getTargetEntitiesForRightsAndLocation($this->getAvailableRights(), $this->getRootLocation());
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer[]
     * @throws \Exception
     */
    public function getTargetUsersForUser(User $user)
    {
        if (!isset($this->targetUsersCache[$user->getId()]))
        {
            $targetGroupIdentifiers = $this->getTargetGroupIdentifiersForUser($user);
            $targetGroups = $this->getGroupService()->findGroupsByIdentifiers($targetGroupIdentifiers);

            $targetUserIdentifiers = [];

            foreach ($targetGroups as $targetGroup)
            {
                //TODO: $targetGroup->get_users() should be re-implemented in the GroupService
                $userIdentifiers = $targetGroup->get_users(true, true);
                $targetUserIdentifiers = array_merge($targetUserIdentifiers, $userIdentifiers);
            }

            $this->targetUsersCache[$user->getId()] = array_unique($targetUserIdentifiers);
        }

        return $this->targetUsersCache[$user->getId()];
    }

    /**
     * @return \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    public function getUserEntityProvider(): UserEntityProvider
    {
        return $this->userEntityProvider;
    }

    /**
     * @param \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider $userEntityProvider
     */
    public function setUserEntityProvider(UserEntityProvider $userEntityProvider): void
    {
        $this->userEntityProvider = $userEntityProvider;
    }

    /**
     * @param integer $userIdentifier
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     * @throws \Exception
     */
    public function isUserIdentifierTargetForUser(int $userIdentifier, User $user)
    {
        return $user->is_platform_admin() || in_array($userIdentifier, $this->getTargetUsersForUser($user));
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[][][] $values
     *
     * @return boolean
     * @throws \Exception
     */
    public function saveRightsConfigurationForUserFromValues(User $user, array $values)
    {
        $rightsLocation = $this->getRootLocation();

        return $this->saveRightsConfigurationForRightsLocationAndUserFromValues($rightsLocation, $user, $values);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[][][] $values
     *
     * @return boolean
     * @throws \Exception
     */
    public function setRightsConfigurationForUserFromValues(User $user, array $values)
    {
        $rightsLocation = $this->getRootLocation();

        if (!$this->setRightsLocationEntityRightsForRightsLocationAndUserFromValues($rightsLocation, $user, $values))
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
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight $rightsLocationEntityRight
     * @param integer[][][] $values
     *
     * @return boolean
     * @throws \Exception
     */
    protected function setRightsLocationEntityRightGroupsForRightsLocationEntityRightFromValues(
        RightsLocationEntityRight $rightsLocationEntityRight, array $values
    )
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
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation $location
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer[][][] $values
     *
     * @return boolean
     * @throws \Exception
     */
    protected function setRightsLocationEntityRightGroupsForRightsLocationFromValues(
        RightsLocation $location, User $user, array $values
    )
    {
        $success = true;

        foreach ($values[RightsForm::PROPERTY_RIGHT_OPTION] as $rightIdentifier => $rightsOption)
        {
            switch ($rightsOption)
            {
                case RightsForm::RIGHT_OPTION_ALL :
                    $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
                        $rightIdentifier, 0, 0, $location->getId()
                    );

                    $success &= $this->setRightsLocationEntityRightGroupsForRightsLocationEntityRightFromValues(
                        $locationEntityRight, $values
                    );
                    break;
                case RightsForm::RIGHT_OPTION_ME :
                    $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
                        $rightIdentifier, $user->getId(), UserEntityProvider::ENTITY_TYPE, $location->getId()
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
                            $locationEntityRight = $this->findRightsLocationEntityRightByParameters(
                                $rightIdentifier, $entityIdentifier, $entityType, $location->getId()
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