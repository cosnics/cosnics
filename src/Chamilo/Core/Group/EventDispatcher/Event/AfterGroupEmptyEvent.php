<?php
namespace Chamilo\Core\Group\EventDispatcher\Event;

use Chamilo\Core\Group\Storage\DataClass\Group;

/**
 * @package Chamilo\Core\Group\EventDispatcher\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AfterGroupEmptyEvent extends AbstractGroupEvent
{
    /**
     * @var string[]
     */
    protected array $impactUserIdentifiers = [];

    public function __construct(Group $group, array $impactUserIdentifiers = [])
    {
        parent::__construct($group);

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