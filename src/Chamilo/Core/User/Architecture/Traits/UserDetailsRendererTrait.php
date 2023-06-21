<?php
namespace Chamilo\Core\User\Architecture\Traits;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Architecture\Traits
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait UserDetailsRendererTrait
{
    protected Translator $translator;

    protected UserService $userService;

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    abstract public function renderUserDetails(User $user, User $requestingUser): string;

    public function renderUserDetailsForUserIdentifier(string $userIdentifier, User $requestingUser): string
    {
        return $this->renderUserDetails(
            $this->getUserService()->findUserByIdentifier($userIdentifier), $requestingUser
        );
    }
}