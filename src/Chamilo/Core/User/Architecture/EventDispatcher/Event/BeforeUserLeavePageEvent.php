<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Event;

use Chamilo\Core\User\Storage\Entity\User;
use Symfony\Component\Uid\Uuid;

/**
 * @package Chamilo\Core\User\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BeforeUserLeavePageEvent extends AbstractUserEvent
{
    public function __construct(
        User $user, public Uuid $userVisitIdentifier, ?User $executingUser = null, bool $flush = true
    )
    {
        parent::__construct($user, $executingUser, $flush);
    }
}