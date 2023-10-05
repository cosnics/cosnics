<?php
namespace Chamilo\Core\User\EventDispatcher\Event;

use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package Chamilo\Core\User\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractUserEvent extends Event
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}