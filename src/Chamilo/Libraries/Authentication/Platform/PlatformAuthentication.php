<?php

namespace Chamilo\Libraries\Authentication\Platform;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Interfaces\ChangeablePassword;
use Chamilo\Libraries\Architecture\Interfaces\ChangeableUsername;
use Chamilo\Libraries\Authentication\Authentication;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\AuthenticationInterface;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Authentication\Platform
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformAuthentication extends Authentication
    implements AuthenticationInterface, ChangeablePassword, ChangeableUsername
{

    /**
     * @var \Chamilo\Libraries\Hashing\HashingUtilities
     */
    protected HashingUtilities $hashingUtilities;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Translator $translator, ChamiloRequest $request,
        UserService $userService, HashingUtilities $hashingUtilities, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($configurationConsulter, $translator, $request, $userService);
        $this->hashingUtilities = $hashingUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Exception
     */
    public function changePassword(User $user, string $oldPassword, string $newPassword): bool
    {
        // Check whether the current password is different from the new password
        if ($oldPassword == $newPassword)
        {
            return false;
        }

        $oldPasswordHash = $this->hashingUtilities->hashString($oldPassword);

        // Verify that the entered old password matches the stored password
        if ($oldPasswordHash != $user->get_password())
        {
            return false;
        }

        // Set the password
        $user->set_password($this->hashingUtilities->hashString($newPassword));

        return $user->update();
    }

    public function getAuthenticationType(): string
    {
        return __NAMESPACE__;
    }

    public function getPasswordRequirements(): string
    {
        return $this->translator->trans('GeneralPasswordRequirements', [], 'Chamilo\Libraries\Authentication\Platform');
    }

    public function getPriority(): int
    {
        return 200;
    }

    /**
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    public function login(): ?User
    {
        $user = $this->getUserFromCredentialsRequest();
        if (!$user instanceof User)
        {
            return null;
        }

        $password = $this->request->getFromPost(self::PARAM_PASSWORD);

        $passwordHash = $this->hashingUtilities->hashString($password);

        if ($user->get_password() == $passwordHash)
        {
            return $user;
        }

        throw new AuthenticationException(
            $this->translator->trans('UsernameOrPasswordIncorrect', [], 'Chamilo\Libraries')
        );
    }

    public function logout(User $user)
    {
        $redirect = new RedirectResponse(
            $this->urlGenerator->fromParameters([], [Application::PARAM_ACTION, Application::PARAM_CONTEXT])
        );

        $redirect->send();
        exit;
    }
}
