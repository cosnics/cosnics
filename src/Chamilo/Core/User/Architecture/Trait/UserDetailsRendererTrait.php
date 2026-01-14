<?php
namespace Chamilo\Core\User\Architecture\Trait;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * @package Chamilo\Core\User\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
        try
        {
            return $this->renderUserDetails(
                $this->getUserService()->findUserByIdentifier($userIdentifier), $requestingUser
            );
        }
        catch (Throwable)
        {
            return '';
        }
    }
}