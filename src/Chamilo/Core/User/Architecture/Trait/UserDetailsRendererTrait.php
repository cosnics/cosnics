<?php
namespace Chamilo\Core\User\Architecture\Trait;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\Uid\Uuid;
use Throwable;

/**
 * @package Chamilo\Core\User\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait UserDetailsRendererTrait
{
    protected UserService $userService;

    abstract public function renderUserDetails(User $user, User $requestingUser): string;

    public function renderUserDetailsForUserIdentifier(string $userIdentifier, User $requestingUser): string
    {
        try {
            return $this->renderUserDetails(
                $this->userService->findUserByIdentifier(Uuid::fromString($userIdentifier)), $requestingUser
            );
        }
        catch (Throwable) {
            return '';
        }
    }
}