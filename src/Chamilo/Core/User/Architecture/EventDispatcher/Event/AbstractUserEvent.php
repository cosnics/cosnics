<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Event;

use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package Chamilo\Core\User\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractUserEvent extends Event
{
    protected ?User $executingUser;

    protected User $user;

    public function __construct(User $user, ?User $executingUser = null)
    {
        $this->user = $user;
        $this->executingUser = $executingUser;
    }

    public function getExecutingUser(): ?User
    {
        return $this->executingUser;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}