<?php
namespace Chamilo\Core\User\EventDispatcher\Event;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AbstractUserAuthenticationEvent extends AbstractUserEvent
{
    protected ?string $clientIpAddress;

    public function __construct(User $user, ?string $clientIpAddress)
    {
        parent::__construct($user);

        $this->clientIpAddress = $clientIpAddress;
    }

    public function getClientIpAddress(): ?string
    {
        return $this->clientIpAddress;
    }

}