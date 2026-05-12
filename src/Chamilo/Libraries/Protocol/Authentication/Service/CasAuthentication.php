<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Throwable;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CasAuthentication extends AbstractCasAuthentication implements AuthenticationInterface
{
    /**
     * @param string[] $casUserAttributes
     */
    protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []): string
    {
        return $casUser;
    }

    public function getPriority(): int
    {
        return 500;
    }

    /**
     * @param string[] $casUserAttributes
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException
     */
    protected function registerUser(string $casUser, array $casUserAttributes = []): User
    {
        $user = new User();

        $user->setUsername($casUser);
        $user->setPassword('PLACEHOLDER');
        $user->setAuthenticationSource(static::class);
        $user->setPlatformAdministrator(false);
        $user->setEmail($casUserAttributes['email']);
        $user->setSurname($casUserAttributes['last_name']);
        $user->setGivenName($casUserAttributes['first_name']);
        $user->setOfficialCode($casUserAttributes['person_number']);

        try {
            $this->userService->createUser($user);

            return $user;
        }
        catch (Throwable) {
            throw new NotAuthenticatedException(
                $this->translator->trans('CasUserRegistrationFailed', [], StringUtilities::LIBRARIES)
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Core\User\Architecture\Exception\NoSuchUserException
     */
    protected function retrieveUserByCasUserIdentifier(string $userIdentifier): ?User
    {
        return $this->userService->retrieveUserByUsername($userIdentifier);
    }
}
