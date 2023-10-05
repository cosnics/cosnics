<?php
namespace Chamilo\Core\User\EventDispatcher\Event;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BeforeUserLeavePageEvent extends AbstractUserEvent
{
    protected string $userVisitIdentifier;

    public function __construct(User $user, string $userVisitIdentifier)
    {
        parent::__construct($user);

        $this->userVisitIdentifier = $userVisitIdentifier;
    }

    public function getUserVisitIdentifier(): string
    {
        return $this->userVisitIdentifier;
    }

}