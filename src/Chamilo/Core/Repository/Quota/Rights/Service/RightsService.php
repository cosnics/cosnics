<?php
namespace Chamilo\Core\Repository\Quota\Rights\Service;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Quota\Rights\Form\RightsGroupForm;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Core\Repository\Quota\Rights\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Rights\Form\RightsForm;
use Chamilo\Libraries\Rights\Storage\Repository\RightsRepository;
use Symfony\Component\Translation\Translator;
use Chamilo\Core\Repository\Quota\Rights\Storage\Repository\RightsRepository as QuotaRightsRepository;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RightsService extends \Chamilo\Libraries\Rights\Service\RightsService
{

    const VIEW_RIGHT = 1;

    /**
     * @var \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    private $userEntityProvider;

    /**
     * @var \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    private $groupEntityProvider;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    private $groupService;

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider $userEntityProvider
     * @param \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider $groupEntityProvider
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     */
    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator,
        UserEntityProvider $userEntityProvider, GroupEntityProvider $groupEntityProvider, GroupService $groupService
    )
    {
        parent::__construct($rightsRepository, $userService, $translator);

        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
        $this->groupService = $groupService;
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\Repository\RightsRepository
     */
    protected function getRightsRepository(): RightsRepository
    {
        return parent::getRightsRepository();
    }

    /**
     * @return integer[]
     */
    public function getAvailableRights()
    {
        return array('View' => self::VIEW_RIGHT);
    }

    /**
     * @return \Chamilo\Libraries\Rights\Interfaces\RightsEntityProvider[]
     */
    public function getAvailableEntities()
    {
        $entities = array();

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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return boolean
     */
    public function canUserConfigureQuotaRequestManagement(User $user)
    {
        return $user->is_platform_admin();
    }

    /**
     * @return integer
     */
    public function countAllRightsLocationEntityRightGroups()
    {
        return $this->getRightsRepository()->countRightsLocationEntityRightGroups();
    }

    /**
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperties
     *
     * @throws \Exception
     */
    public function getRightsLocationEntityRightGroupsWithEntityAndGroup(
        int $offset = null, int $count = null, array $orderProperties = array()
    )
    {
        $groupRecords = $this->getRightsRepository()->findRightsLocationEntityRightGroupsWithEntityAndGroupRecords();

        foreach ($groupRecords as &$groupRecord)
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
            }
            else
            {
                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_TITLE] = $this->getTranslator()->trans('Everyone');
                $groupRecord[EntityTableColumnModel::PROPERTY_ENTITY_DESCRIPTION] = '';
            }

            $group =
                $this->getGroupService()->findGroupByIdentifier($groupRecord[QuotaRightsRepository::PROPERTY_GROUP_ID]);

            $groupRecord[EntityTableColumnModel::PROPERTY_GROUP_NAME] = $group->get_name();
            $groupRecord[EntityTableColumnModel::PROPERTY_GROUP_PATH] = $this->getGroupService()->getGroupPath($group);
        }

        $orderProperty = array_shift($orderProperties);
        $orderPropertyValue = $orderProperty->getConditionVariable()->get_value();
        $orderDirection = $orderProperty->getDirection();

        $groupRecords->uasort(
            function ($groupRecordOne, $groupRecordTwo) use ($orderPropertyValue, $orderDirection) {

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

        return $groupRecords;
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

}