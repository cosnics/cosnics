<?php
namespace Chamilo\Core\User\Architecture\EventDispatcher\Event;

use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Core\User\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterUserEnterPageEvent extends AbstractUserEvent
{
    protected string $pageUri;

    public function __construct(User $user, string $pageUri, ?User $executingUser = null)
    {
        parent::__construct($user, $executingUser);

        $this->pageUri = $pageUri;
    }

    public function getPageUri(): string
    {
        return $this->pageUri;
    }
}