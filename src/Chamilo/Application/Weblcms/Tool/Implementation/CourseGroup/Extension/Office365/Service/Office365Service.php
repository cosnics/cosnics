<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use JsonSchema\Exception\ResourceNotFoundException;

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
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository\Office365Repository $office365Repository
    )
    {
        $this->office365Repository = $office365Repository;
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

    public function isMemberOfGroup($groupId, User $user)
    {
        return $this->getGroupMember($groupId, $user) instanceof \Microsoft\Graph\Model\User;
    }

    public function getGroupMember($groupId, User $user)
    {
        $office365UserIdentifier = $this->getOffice365UserIdentifier($user);

        return $this->office365Repository->getGroupMember($groupId, $office365UserIdentifier);
    }

//    /**
//     * Gets an access token
//     *
//     * @return string
//     */
//    protected function getAccessToken()
//    {
//        $accessToken = $this->localSetting->get(
//            'access_token', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
//        );
//
//        if(empty($accessToken))
//        {
//            $accessToken = $this->requestNewAccessToken();
//        }
//
//        return $accessToken;
//    }
//
//    /**
//     * Requests a new access token from office365 and stores them in the settings
//     *
//     * @return string
//     */
//    protected function requestNewAccessToken()
//    {
//        $accessToken = $this->office365Repository->getAccessToken();
//
//        $this->localSetting->create(
//            'access_token', $accessToken,
//            'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
//        );
//
//        return $accessToken->getToken();
//    }

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
            $office365UserIdentifier = $this->office365Repository->getOffice365UserIdentifier($user);

            $this->localSetting->create(
                'external_user_id', $office365UserIdentifier,
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
            );
        }

        return $office365UserIdentifier;
    }
}