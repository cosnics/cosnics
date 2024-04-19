<?php

namespace Chamilo\Application\Weblcms\API\Component;

use Chamilo\Application\Weblcms\API\Manager;
use Chamilo\Application\Weblcms\API\Model\APIUser;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetUserComponent extends Manager
{

    function run(): JsonResponse
    {
        $user = $this->getUserService()->findUserByIdentifier($this->get_parameter(('user_id')));
        if(!$user instanceof User)
        {
            throw new ObjectNotExistException('User not found');
        }

        $apiUser = $this->toAPIUser($user);

        return new JsonResponse($this->getSerializer()->serialize($apiUser, 'json'), 200, [], true);
    }

    protected function toAPIUser(User $user): APIUser
    {
        $apiUser = new APIUser();

        $apiUser->setId($user->getId());
        $apiUser->setLoginId($user->get_username());
        $apiUser->setFirstName($user->get_firstname());
        $apiUser->setLastName($user->get_lastname());
        $apiUser->setName($user->get_firstname() . ' ' . $user->get_lastname());
        $apiUser->setSortableName(str_replace(' ', '', ($user->get_lastname() . ',' . $user->get_firstname())));
        $apiUser->setEmail($user->get_email());
        $apiUser->setLocale('nl'); //todo: get locale from user settings
        $apiUser->setTimeZone(date_default_timezone_get());

        return $apiUser;
    }

    protected function getUserService(): UserService
    {
        return $this->getService(UserService::class);
    }
}