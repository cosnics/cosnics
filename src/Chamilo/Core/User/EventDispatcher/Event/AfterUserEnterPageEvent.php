<?php
namespace Chamilo\Core\User\EventDispatcher\Event;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\User\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterUserEnterPageEvent extends AbstractUserEvent
{
    protected string $pageUri;

    public function __construct(User $user, string $pageUri)
    {
        parent::__construct($user);

        $this->pageUri = $pageUri;
    }

    public function getPageUri(): string
    {
        return $this->pageUri;
    }

}