<?php
namespace Chamilo\Libraries\Protocol\Authentication\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeablePasswordInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeableUsernameInterface;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class PlatformAuthentication extends Authentication
    implements AuthenticationInterface, ChangeablePasswordInterface, ChangeableUsernameInterface
{
    /**
     * @var \Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm
     */
    protected HashingAlgorithm $hashingUtilities;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        Translator $translator, ChamiloRequest $request, UserService $userService,
        AuthenticationValidator $authenticationValidator, HashingAlgorithm $hashingUtilities, UrlGenerator $urlGenerator
    )
    {
        parent::__construct($translator, $request, $userService, $authenticationValidator);
        $this->hashingUtilities = $hashingUtilities;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Exception
     */
    public function changePassword(User $user, string $oldPassword, string $newPassword): bool
    {
        // Check whether the current password is different from the new password
        if ($oldPassword == $newPassword) {
            return false;
        }

        $hashingUtilities = $this->getHashingUtilities();

        $oldPasswordHash = $hashingUtilities->hashString($oldPassword);

        // Verify that the entered old password matches the stored password
        if ($oldPasswordHash != $user->getPassword()) {
            return false;
        }

        // Set the password
        $user->setPassword($hashingUtilities->hashString($newPassword));

        return $this->getUserService()->updateUser($user);
    }

    public function getHashingUtilities(): HashingAlgorithm
    {
        return $this->hashingUtilities;
    }

    public function getPasswordRequirements(): string
    {
        return $this->translator->trans('GeneralPasswordRequirements', [], 'Chamilo\Libraries\Authentication\Platform');
    }

    public function getPriority(): int
    {
        return 200;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\AuthenticationException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function login(bool $checkIfAuthenticationSourceIsEnabled = true): ?User
    {
        $this->checkAuthenticationSource($checkIfAuthenticationSourceIsEnabled);

        $user = $this->getUserFromCredentialsRequest();
        if (!$user instanceof User) {
            return null;
        }

        $password = $this->getRequest()->request->get(self::PARAM_PASSWORD);
        $passwordHash = $this->getHashingUtilities()->hashString($password);

        if ($user->getPassword() == $passwordHash) {
            return $user;
        }

        throw new AuthenticationException(
            $this->getTranslator()->trans('UsernameOrPasswordIncorrect', [], StringUtilities::LIBRARIES)
        );
    }

    public function logout(User $user): void
    {
        $redirect = new RedirectResponse(
            $this->getUrlGenerator()->fromParameters([], [Application::PARAM_ACTION, Application::PARAM_CONTEXT])
        );

        $redirect->send();
        exit;
    }
}
