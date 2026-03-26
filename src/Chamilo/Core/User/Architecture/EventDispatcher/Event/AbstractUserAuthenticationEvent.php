<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Event;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AbstractUserAuthenticationEvent extends AbstractUserEvent
{
    protected ?string $clientIpAddress;

    public function __construct(User $user, ?string $clientIpAddress, ?User $executingUser = null)
    {
        parent::__construct($user, $executingUser);

        $this->clientIpAddress = $clientIpAddress;
    }

    public function getClientIpAddress(): ?string
    {
        return $this->clientIpAddress;
    }
}