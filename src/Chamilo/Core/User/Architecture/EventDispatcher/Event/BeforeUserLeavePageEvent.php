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
    protected Uuid $userVisitIdentifier;

    public function __construct(User $user, Uuid $userVisitIdentifier, ?User $executingUser = null)
    {
        parent::__construct($user, $executingUser);

        $this->userVisitIdentifier = $userVisitIdentifier;
    }

    public function getUserVisitIdentifier(): Uuid
    {
        return $this->userVisitIdentifier;
    }
}