<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAuthenticatedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CasAuthentication extends AbstractCasAuthentication implements AuthenticationInterface
{
    protected function getCasUserIdentifierFromAttributes(string $casUser, array $casUserAttributes = []): string
    {
        return $casUser;
    }

    public function getPriority(): int
    {
        return 500;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getUserByCasUserIdentifier(string $userIdentifier): ?User
    {
        return $this->getUserService()->findUserByUsername($userIdentifier);
    }

    /**
     * @param string $casUser
     * @param string[] $casUserAttributes
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
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

        if (!$this->getUserService()->createUser($user)) {
            throw new NotAuthenticatedException(
                $this->getTranslator()->trans('CasUserRegistrationFailed', [], StringUtilities::LIBRARIES)
            );
        }
        else {
            return $user;
        }
    }
}
