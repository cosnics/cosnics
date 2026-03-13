<?php
namespace Chamilo\Core\Group\Architecture\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Group\Architecture\EventDispatcher\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterGroupDeleteEvent extends AbstractGroupEvent
{
    /**
     * @var string[]
     */
    protected array $impactUserIdentifiers = [];

    /**
     * @var string[]
     */
    protected array $subGroupIdentifiers = [];

    public function __construct(Group $group, array $subGroupIdentifiers = [], array $impactUserIdentifiers = [], ?User $executingUser = null)
    {
        parent::__construct($group, $executingUser);

        $this->subGroupIdentifiers = $subGroupIdentifiers;
        $this->impactUserIdentifiers = $impactUserIdentifiers;
    }

    /**
     * @return string[]
     */
    public function getImpactUserIdentifiers(): array
    {
        return $this->impactUserIdentifiers;
    }

    /**
     * @return string[]
     */
    public function getSubGroupIdentifiers(): array
    {
        return $this->subGroupIdentifiers;
    }

}