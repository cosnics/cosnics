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
    public function __construct(public User $user, public ?User $executingUser = null, public bool $flush = true)
    {
    }
}