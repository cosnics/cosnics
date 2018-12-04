<?php
namespace Chamilo\Core\Repository\Quota\Rights\Service;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocation;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Rights\Storage\Repository\RightsRepository;
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
     * @var \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider
     */
    private $userEntityProvider;

    /**
     * @var \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider
     */
    private $groupEntityProvider;

    /**
     * @param \Chamilo\Libraries\Rights\Storage\Repository\RightsRepository $rightsRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider $userEntityProvider
     * @param \Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider $groupEntityProvider
     */
    public function __construct(
        RightsRepository $rightsRepository, UserService $userService, Translator $translator,
        UserEntityProvider $userEntityProvider, GroupEntityProvider $groupEntityProvider
    )
    {
        parent::__construct($rightsRepository, $userService, $translator);

        $this->userEntityProvider = $userEntityProvider;
        $this->groupEntityProvider = $groupEntityProvider;
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
}