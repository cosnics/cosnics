<?php
namespace Chamilo\Core\User\EventDispatcher\Event;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AbstractUserTransferEvent extends AbstractUserEvent
{
    protected User $transferUser;

    public function __construct(User $user, User $transferUser)
    {
        parent::__construct($user);

        $this->transferUser = $transferUser;
    }

    public function getTransferUser(): User
    {
        return $this->transferUser;
    }

}