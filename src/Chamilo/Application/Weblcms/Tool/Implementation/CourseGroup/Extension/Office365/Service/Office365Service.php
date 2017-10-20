<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365\Storage\Repository;

use Chamilo\Core\User\Storage\DataClass\User;

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
        $accessToken = $this->getAccessToken();
        $office365UserIdentifier = $this->getOffice365UserIdentifier($owner);

        $group = $this->office365Repository->createGroup($accessToken, $groupName);
        $this->office365Repository->subscribeOwnerInGroup($accessToken, $group, $office365UserIdentifier);
    }

    /**
     * Gets an access token
     *
     * @return string
     */
    protected function getAccessToken()
    {
        $accessToken = $this->localSetting->get(
            'access_token', 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
        );

        if(!$this->office365Repository->isAccessTokenValid($accessToken))
        {
            try
            {
                $accessToken = $this->office365Repository->refreshAccessToken($accessToken);
            }
            catch(\Exception $ex)
            {
                $accessToken = $this->office365Repository->getAccessToken();
            }

            $this->localSetting->create(
                'access_token', $accessToken,
                'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Extension\Office365'
            );
        }

        return $accessToken;
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

        if(empty($office365UserIdentifier))
        {
            $office365UserIdentifier = $this->office365Repository->getOffice365UserIdentifier($user);
        }

        return $office365UserIdentifier;
    }
}