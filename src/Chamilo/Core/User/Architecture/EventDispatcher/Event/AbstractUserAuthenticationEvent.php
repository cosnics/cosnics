<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Event;

use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Core\User\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractUserAuthenticationEvent extends AbstractUserEvent
{
    public function __construct(
        User $user, public ?string $clientIpAddress, ?User $executingUser = null, bool $flush = true
    )
    {
        parent::__construct($user, $executingUser, $flush);
    }
}