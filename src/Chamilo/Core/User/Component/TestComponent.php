<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserInviteService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\User\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TestComponent extends Manager
{

    /**
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Core\User\Domain\UserInvite\Exceptions\UserAlreadyExistsException
     */
    function run()
    {
        if(!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();

        }

        $this->getUserInviteService()->inviteUser($this->getUser(), 'test@hogent.be', "Hey Robert\n\n het zou fijn zijn mocht je mijn portfolio even kunnen bekijken.\n\nGroetjes Sven");
    }

    /**
     * @return UserInviteService
     */
    protected function getUserInviteService()
    {
        return $this->getService(UserInviteService::class);
    }
}