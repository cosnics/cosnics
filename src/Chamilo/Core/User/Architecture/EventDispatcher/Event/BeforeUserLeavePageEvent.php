<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Event;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BeforeUserLeavePageEvent extends AbstractUserEvent
{
    protected string $userVisitIdentifier;

    public function __construct(User $user, string $userVisitIdentifier, ?User $executingUser = null)
    {
        parent::__construct($user, $executingUser);

        $this->userVisitIdentifier = $userVisitIdentifier;
    }

    public function getUserVisitIdentifier(): string
    {
        return $this->userVisitIdentifier;
    }
}