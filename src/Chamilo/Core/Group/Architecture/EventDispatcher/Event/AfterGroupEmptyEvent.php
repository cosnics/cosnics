<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterGroupEmptyEvent extends AbstractGroupEvent
{
    /**
     * @var string[]
     */
    protected array $impactUserIdentifiers = [];

    public function __construct(Group $group, array $impactUserIdentifiers = [], ?User $executingUser = null)
    {
        parent::__construct($group, $executingUser);

        $this->impactUserIdentifiers = $impactUserIdentifiers;
    }

    /**
     * @return string[]
     */
    public function getImpactUserIdentifiers(): array
    {
        return $this->impactUserIdentifiers;
    }
}