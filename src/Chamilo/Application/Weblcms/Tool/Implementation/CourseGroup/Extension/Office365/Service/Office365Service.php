<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365Service
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository
     */
    protected $office365Repository;

    /**
     * @var \Chamilo\Libraries\Platform\Configuration\LocalSetting
     */
    protected $localSetting;

    /**
     * Office365Service constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository $office365Repository
     * @param \Chamilo\Libraries\Platform\Configuration\LocalSetting $localSetting
     */
    public function __construct(Office365Repository $office365Repository, LocalSetting $localSetting)
    {
        $this->office365Repository = $office365Repository;
        $this->localSetting = $localSetting;
    }

    /**
     * Creates a group by a given name
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $groupName
     */
    public function createGroupByName(User $owner, $groupName)
    {
        $office365UserIdentifier = $this->getOffice365UserIdentifier($owner);

        $group = $this->office365Repository->createGroup($groupName);
        $this->office365Repository->subscribeMemberInGroup($group, $office365UserIdentifier);
    }

    /**
     * Adds a member to a group. Checking if the user is already subscribed or not.
     *
     * @param string $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function addMemberToGroup($groupId, User $user)
    {
        if (!$this->isMemberOfGroup($groupId, $user))
        {
            $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

            $this->office365Repository->subscribeMemberInGroup($groupId, $office365UserIdentifier);
        }
    }

    /**
     * Returns whether or not the given user is subscribed to the given group
     *
     * @param int $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function isMemberOfGroup($groupId, User $user)
    {
        return $this->getGroupMember($groupId, $user) instanceof \Microsoft\Graph\Model\User;
    }

    /**
     * Returns the group member object of a user in a given group
     *
     * @param int $groupId
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Microsoft\Graph\Model\User
     */
    public function getGroupMember($groupId, User $user)
    {
        $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

        return $this->office365Repository->getGroupMember($groupId, $office365UserIdentifier);
    }

    /**
     * Returns the identifier in office365 for a given user
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    protected function getOffice365UserIdentifier(User $user)
    {
        $office365UserIdentifier = $this->localSetting->get(
            'external_user_id', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
        );

        if (empty($office365UserIdentifier))
        {
            $office365User = $this->office365Repository->getOffice365User($user);
            $office365UserIdentifier = $office365User->getId();

            $this->localSetting->create(
                'external_user_id', $office365UserIdentifier,
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
            );
        }

        return $office365UserIdentifier;
    }
}