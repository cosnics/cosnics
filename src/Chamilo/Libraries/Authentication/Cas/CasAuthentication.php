<?php
namespace Chamilo\Libraries\Authentication\Cas;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;

/**
 * @package Chamilo\Libraries\Authentication\Cas
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CasAuthentication extends AbstractCasAuthentication implements AuthenticationInterface
{
    public function getAuthenticationType(): string
    {
        return __NAMESPACE__;
    }

    protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []): string
    {
        return $casUser;
    }

    public function getPriority(): int
    {
        return 500;
    }

    protected function getUserByCasUserIdentifier(string $userIdentifier): ?User
    {
        return $this->getUserService()->findUserByUsername($userIdentifier);
    }

    /**
     * @param string $casUser
     * @param string[] $casUserAttributes
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     * @throws \Exception
     */
    protected function registerUser(string $casUser, array $casUserAttributes = []): User
    {
        $user = new User();

        $user->set_username($casUser);
        $user->set_password('PLACEHOLDER');
        $user->set_status(User::STATUS_STUDENT);
        $user->set_auth_source(__NAMESPACE__);
        $user->set_platformadmin(0);
        $user->set_email($casUserAttributes['email']);
        $user->set_lastname($casUserAttributes['last_name']);
        $user->set_firstname($casUserAttributes['first_name']);
        $user->set_official_code($casUserAttributes['person_number']);

        if (!$user->create())
        {
            throw new AuthenticationException('CasUserRegistrationFailed');
        }
        else
        {
            return $user;
        }
    }
}
